<?php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JobcardController extends Controller
{
    public function index(Request $request)
    {
        $query = Jobcard::with('client');
        
        // Search by client name
        if ($request->filled('client')) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%')
                  ->orWhere('surname', 'like', '%' . $request->client . '%');
            });
        }
        
        // Search by jobcard number
        if ($request->filled('jobcard_number')) {
            $query->where('jobcard_number', 'like', '%' . $request->jobcard_number . '%');
        }
        
        // Search by date range
        if ($request->filled('date_from')) {
            $query->where('job_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('job_date', '<=', $request->date_to);
        }
        
        // Search by specific date
        if ($request->filled('date')) {
            $query->whereDate('job_date', $request->date);
        }
        
        // Search by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Order by most recent first
        $query->orderBy('job_date', 'desc')->orderBy('created_at', 'desc');
        
        // Handle AJAX requests for infinite scroll
        if ($request->ajax()) {
            $jobcards = $query->paginate(20);
            return response()->json([
                'data' => $jobcards->items(),
                'next_page_url' => $jobcards->nextPageUrl(),
                'has_more_pages' => $jobcards->hasMorePages()
            ]);
        }
        
        // Regular page load
        $jobcards = $query->paginate(20);
        
        return view('jobcard.index', compact('jobcards'));
    }

    public function show(Jobcard $jobcard)
    {
        // Load relationships carefully
        $jobcard->load(['client', 'inventory']);
        
        // Only load employees if they exist
        if ($jobcard->employees()->exists()) {
            $jobcard->load('employees');
        }
        
        // Add missing variables that the blade template expects
        $employees = Employee::all();
        $inventory = Inventory::all();
        $clients = Client::all();
        // Add assignedInventory for blade
        $assignedInventory = $jobcard->inventory->map(function($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->pivot->quantity ?? 1,
                'name' => $item->name ?? $item->description
            ];
        })->toArray();
        return view('livewire.jobcard-editor', compact('jobcard', 'employees', 'inventory', 'clients', 'assignedInventory'));
    }

    public function create()
    {
        $employees = Employee::all();
        $inventory = Inventory::all();
        return view('jobcard.create', compact('employees', 'inventory'));
    }

    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'client_id' => 'required',
            'category' => 'required|string',
            'job_date' => 'required|date',
            'work_request' => 'nullable|string',
            'special_request' => 'nullable|string',
            'photos.*' => 'nullable|image|max:15360' // 15MB max per photo
        ]);

        DB::transaction(function () use ($request) {
            $clientId = $request->input('client_id');
            // Handle temporary client creation
            if ($clientId === 'temp') {
                $client = new \App\Models\Client();
                $client->name = $request->input('temp_client_name');
                $client->surname = $request->input('temp_client_surname');
                $client->telephone = $request->input('temp_client_telephone') ?: '-';
                $client->address = $request->input('temp_client_address');
                $client->email = $request->input('temp_client_email');
                $client->is_active = false;
                // Add is_temporary if not present in DB, else use notes
                if (Schema::hasColumn('clients', 'is_temporary')) {
                    $client->is_temporary = true;
                } else {
                    $client->notes = ($client->notes ? $client->notes . ' ' : '') . '[TEMPORARY CLIENT]';
                }
                $client->save();
                $clientId = $client->id;
            }

            $data = $request->only([
                'jobcard_number', 'job_date', 'category', 'work_request', 'special_request',
                'status', 'work_done', 'time_spent',
                'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
                'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
            ]);

            // Set is_quote based on category
            $data['is_quote'] = ($data['category'] === 'Quote');
            
            // Ensure category has a default value if null or empty
            if (empty($data['category'])) {
                $data['category'] = 'General Maintenance';
            }

            // Ensure jobcard number exists - if not, generate one based on category
            if (empty($data['jobcard_number'])) {
                $now = now();
                $timestamp = $now->format('Ymdhi');
                
                // Get abbreviation based on category
                $abbreviations = [
                    'General Maintenance' => 'gm',
                    'Emergency Repair' => 'er',
                    'Installation' => 'in',
                    'Call Out' => 'ca',
                    'Preventive Maintenance' => 'pm',
                    'Inspection' => 'is',
                    'Quote' => 'qt'
                ];
                
                $abbreviation = $abbreviations[$data['category']] ?? 'xx';
                $data['jobcard_number'] = $abbreviation . '-' . $timestamp;
            }

            // Jobcard number is now generated on the frontend based on category
            // No need for backend generation

            $jobcard = Jobcard::create(array_merge($data, ['client_id' => $clientId]));

            // --- EMPLOYEES ---
            $syncData = [];
            if ($request->has('employees')) {
                foreach ($request->input('employees', []) as $employee) {
                    if (!empty($employee['id'])) {
                        $syncData[$employee['id']] = [
                            'hours_worked' => $employee['hours'] ?? 0,
                            'hour_type' => $employee['hour_type'] ?? 'normal',
                        ];
                    }
                }
            }
            
            if (!empty($syncData)) {
                $jobcard->employees()->sync($syncData);
            }

            // --- TRAVELING ---
            if ($request->has('traveling')) {
                foreach ($request->input('traveling', []) as $travel) {
                    if (!empty($travel['id'])) {
                        // Attach as a separate pivot row with hour_type = 'traveling'
                        $jobcard->employees()->attach($travel['id'], [
                            'hours_worked' => 0,
                            'hour_type' => 'traveling',
                            'travel_km' => $travel['km'] ?? 0,
                        ]);
                    }
                }
            }

            // --- INVENTORY ---
            if ($request->has('inventory')) {
                $syncData = [];
                foreach ($request->input('inventory', []) as $item) {
                    if (!empty($item['id'])) {
                        $inv = \App\Models\Inventory::find($item['id']);
                        $syncData[$item['id']] = [
                            'quantity' => $item['qty'] ?? 1,
                            'buying_price' => $inv ? $inv->buying_price : null,
                            'selling_price' => $inv ? $inv->selling_price : null,
                        ];
                    }
                }
                $jobcard->inventory()->sync($syncData);
            }
        });

        return redirect()->route('jobcard.index')->with('success', 'Jobcard created successfully!');
    }

    public function edit(Jobcard $jobcard)
    {
        $jobcard->load(['client', 'employees', 'inventory']);
        $employees = Employee::all();
        $inventory = Inventory::all();
        $clients = Client::all();
        // Add assignedInventory for blade
        $assignedInventory = $jobcard->inventory->map(function($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->pivot->quantity ?? 1,
                'name' => $item->name ?? $item->description
            ];
        })->toArray();
        return view('livewire.jobcard-editor', compact('jobcard', 'employees', 'inventory', 'clients', 'assignedInventory'));
    }

    public function update(Request $request, Jobcard $jobcard)
    {
        DB::transaction(function () use ($request, $jobcard) {
            // Update basic jobcard fields
            $jobcard->update($request->only([
                'jobcard_number', 'job_date', 'client_id', 'category', 
                'work_request', 'special_request', 'status', 'work_done',
                'normal_hours', 'overtime_hours', 'weekend_hours', 
                'public_holiday_hours', 'call_out_hours', 'total_labour_cost',
                'is_quote'
            ]));
            // Clean up duplicate pivot entries for this jobcard
            $pivotTable = 'employee_jobcard';
            $jobcardId = $jobcard->id;
            DB::statement("
                DELETE t1 FROM $pivotTable t1
                INNER JOIN $pivotTable t2
                WHERE
                    t1.id > t2.id
                    AND t1.employee_id = t2.employee_id
                    AND t1.jobcard_id = t2.jobcard_id
                    AND t1.hour_type = t2.hour_type
                    AND t1.jobcard_id = ?
            ", [$jobcardId]);

            // Detach all existing employee_jobcard entries for this jobcard
            $jobcard->employees()->detach();

            // Attach all current entries (including traveling)
            $attachData = [];
            // Non-traveling employees
            if ($request->has('employees') && !empty($request->employees)) {
                foreach ($request->employees as $employeeId) {
                    $hourType = $request->employee_hour_types[$employeeId] ?? 'normal';
                    $hours = $request->employee_hours[$employeeId] ?? 0;
                    $travelKm = ($hourType === 'traveling') ? ($request->traveling_km[$employeeId] ?? 0) : null;
                    $attachData[] = [
                        'employee_id' => $employeeId,
                        'hours_worked' => $hours,
                        'hour_type' => $hourType,
                        'travel_km' => $travelKm
                    ];
                }
            }
            // Traveling employees (ensure not duplicated)
            if ($request->has('traveling_employees')) {
                foreach ($request->traveling_employees as $employeeId) {
                    // Only add if not already in attachData as traveling
                    $already = collect($attachData)->first(function($row) use ($employeeId) {
                        return $row['employee_id'] == $employeeId && $row['hour_type'] == 'traveling';
                    });
                    if (!$already) {
                        $travelKm = $request->traveling_km[$employeeId] ?? 0;
                        $attachData[] = [
                            'employee_id' => $employeeId,
                            'hours_worked' => 0,
                            'hour_type' => 'traveling',
                            'travel_km' => $travelKm
                        ];
                    }
                }
            }
            // Attach all
            foreach ($attachData as $row) {
                $jobcard->employees()->attach($row['employee_id'], [
                    'hours_worked' => $row['hours_worked'],
                    'hour_type' => $row['hour_type'],
                    'travel_km' => $row['travel_km']
                ]);
            }

            // After handling traveling, update mileage_km on jobcard
            $totalTravelKm = $jobcard->employees()->wherePivot('hour_type', 'traveling')->sum('employee_jobcard.travel_km');
            $jobcard->mileage_km = $totalTravelKm;
            $jobcard->save();

            // Handle deletions for employees (all hour types except traveling)
            if ($request->filled('deleted_employees')) {
                $ids = array_filter(explode(',', $request->deleted_employees));
                if (!empty($ids)) {
                    $jobcard->employees()->wherePivotIn('employee_id', $ids)->wherePivot('hour_type', '!=', 'traveling')->detach();
                }
            }
            // Handle deletions for traveling entries
            if ($request->filled('deleted_traveling')) {
                $ids = array_filter(explode(',', $request->deleted_traveling));
                if (!empty($ids)) {
                    $jobcard->employees()->wherePivotIn('employee_id', $ids)->wherePivot('hour_type', 'traveling')->detach();
                }
            }

            // Handle inventory - support both old and new formats
            if ($request->has('inventory_items')) {
                $syncData = [];
                foreach ($request->inventory_items as $itemId) {
                    $qty = $request->inventory_qty[$itemId] ?? 1;
                    $syncData[$itemId] = ['quantity' => $qty];
                }
                $jobcard->inventory()->sync($syncData);
            } elseif ($request->has('inventory_data')) {
                // Handle JSON inventory data from Livewire
                $inventoryData = json_decode($request->inventory_data, true);
                if (is_array($inventoryData)) {
                    $syncData = [];
                    foreach ($inventoryData as $item) {
                        $syncData[$item['id']] = ['quantity' => $item['quantity']];
                    }
                    $jobcard->inventory()->sync($syncData);
                }
            }
            // Handle photo uploads (system side)
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('jobcard_photos', 'public');
                    \App\Models\MobileJobcardPhoto::create([
                        'jobcard_id' => $jobcard->id,
                        'file_path' => $path,
                    ]);
                }
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Jobcard updated!']);
        }
        return redirect()->route('jobcard.show', $jobcard->id)->with('success', 'Jobcard updated successfully!');
    }

    public function submitForInvoice(Jobcard $jobcard)
    {
        // Example: mark as invoiced and create invoice
        $jobcard->status = 'invoiced';
        $jobcard->save();

        $invoice = Invoice::create([
            'jobcard_id' => $jobcard->id,
            'client_id' => $jobcard->client_id,
            // ...other invoice fields...
        ]);

        return redirect()->route('invoice.show', $invoice->id)->with('success', 'Invoice created!');
    }

    public function updateProgress(Request $request, $id)
    {
        $jobcard = Jobcard::findOrFail($id);

        if ($request->has('action')) {
            if ($request->action === 'completed') {
                $jobcard->status = 'completed';
                $jobcard->save();
            }

            if ($request->action === 'invoice') {
                // Prevent duplicate invoices
                if (!$jobcard->invoice_number) {
                    Invoice::create([
                        'jobcard_id'     => $jobcard->id,
                        'client_id'      => $jobcard->client_id,
                        'amount'         => $jobcard->amount ?? 0,
                        'status'         => 'unpaid',
                        'invoice_number' => $jobcard->jobcard_number,
                        'invoice_date'   => now()->toDateString(),
                    ]);
                    $jobcard->status = 'invoiced';
                    $jobcard->invoice_number = $jobcard->jobcard_number;
                    $jobcard->save();
                }
            }
        }

        return redirect()->route('progress')->with('success', 'Jobcard updated!');
    }

    public function generatePDF($id)
    {
        $jobcard = Jobcard::with(['client', 'employees', 'inventory'])->findOrFail($id);
        
        // Calculate totals
        $totalHours = $jobcard->employees->sum('pivot.hours_worked');
        $totalInventoryItems = $jobcard->inventory->sum('pivot.quantity');
        
        $pdf = PDF::loadView('jobcard.pdf', compact('jobcard', 'totalHours', 'totalInventoryItems'));
        
        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        
        return $pdf->download('jobcard-' . $jobcard->jobcard_number . '.pdf');
    }

    public function calculateHourCosts(Request $request)
    {
        $company = \App\Models\CompanyDetail::first();
        
        $normalHours = floatval($request->normal_hours ?? 0);
        $overtimeHours = floatval($request->overtime_hours ?? 0);
        $weekendHours = floatval($request->weekend_hours ?? 0);
        $holidayHours = floatval($request->public_holiday_hours ?? 0);
        $callOutFee = floatval($request->call_out_fee ?? 0);
        $mileageKm = floatval($request->mileage_km ?? 0);
        
        $normalCost = $normalHours * $company->labour_rate;
        $overtimeCost = $overtimeHours * ($company->labour_rate * $company->overtime_multiplier);
        $weekendCost = $weekendHours * ($company->labour_rate * $company->weekend_multiplier);
        $holidayCost = $holidayHours * ($company->labour_rate * $company->public_holiday_multiplier);
        $mileageCost = $mileageKm * $company->mileage_rate;
        
        $totalLabour = $normalCost + $overtimeCost + $weekendCost + $holidayCost;
        $totalWithExtras = $totalLabour + $callOutFee + $mileageCost;
        
        return response()->json([
            'normal_cost' => number_format($normalCost, 2),
            'overtime_cost' => number_format($overtimeCost, 2),
            'weekend_cost' => number_format($weekendCost, 2),
            'holiday_cost' => number_format($holidayCost, 2),
            'mileage_cost' => number_format($mileageCost, 2),
            'total_labour' => number_format($totalLabour, 2),
            'total_with_extras' => number_format($totalWithExtras, 2),
            'rates' => [
                'labour_rate' => number_format((float) $company->labour_rate, 2),
                'overtime_rate' => number_format((float) $company->labour_rate * (float) $company->overtime_multiplier, 2),
                'weekend_rate' => number_format((float) $company->labour_rate * (float) $company->weekend_multiplier, 2),
                'holiday_rate' => number_format((float) $company->labour_rate * (float) $company->public_holiday_multiplier, 2),
                'mileage_rate' => number_format((float) $company->mileage_rate, 2),
            ]
        ]);
    }

    // When attaching an employee to a jobcard:
    public function attachEmployee(Request $request, Jobcard $jobcard)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'hours_worked' => 'required|numeric|min:0',
            'hour_type' => 'required|in:normal,overtime,weekend,public_holiday,call_out',
        ]);
        
        $employee = Employee::find($validated['employee_id']);
        $hourlyRate = $employee->getHourlyRate($validated['hour_type']);
        $totalCost = $validated['hours_worked'] * $hourlyRate;
        
        $jobcard->employees()->attach($validated['employee_id'], [
            'hours_worked' => $validated['hours_worked'],
            'hour_type' => $validated['hour_type'],
            'hourly_rate' => $hourlyRate,
            'total_cost' => $totalCost,
        ]);
        
        // Recalculate jobcard totals
        $jobcard->calculateLaborCosts();
        
        return redirect()->back()->with('success', 'Employee added to jobcard successfully!');
    }
}
