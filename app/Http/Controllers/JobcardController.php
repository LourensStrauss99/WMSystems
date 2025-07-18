<?php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JobcardController extends Controller
{
    // API: List jobcards assigned to an employee (by phone or employee_id)
    public function apiAssignedJobcards(Request $request)
    {
        $phone = $request->input('phone');
        $employeeId = $request->input('employee_id');
        $employee = Employee::query()
            ->when($phone, fn($q) => $q->where('telephone', $phone))
            ->when($employeeId, fn($q) => $q->orWhere('employee_id', $employeeId))
            ->first();
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        $jobcards = $employee->jobcards()->with(['client', 'inventory', 'employees'])->get();
        return response()->json(['jobcards' => $jobcards]);
    }

    // API: View a single jobcard (by jobcard id)
    public function apiViewJobcard($id)
    {
        $jobcard = Jobcard::with(['client', 'inventory', 'employees'])->find($id);
        if (!$jobcard) {
            return response()->json(['error' => 'Jobcard not found'], 404);
        }
        return response()->json(['jobcard' => $jobcard]);
    }

    // API: Update a jobcard (by jobcard id)
    public function apiUpdateJobcard(Request $request, $id)
    {
        $jobcard = Jobcard::find($id);
        if (!$jobcard) {
            return response()->json(['error' => 'Jobcard not found'], 404);
        }
        $jobcard->update($request->only([
            'status', 'work_done', 'progress_note', 'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
            'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
        ]));
        // Optionally update employees/inventory if provided
        if ($request->has('employees')) {
            $syncData = [];
            foreach ($request->employees as $employee) {
                $syncData[$employee['id']] = [
                    'hours_worked' => $employee['hours_worked'] ?? 0,
                    'hour_type' => $employee['hour_type'] ?? 'normal'
                ];
            }
            $jobcard->employees()->sync($syncData);
        }
        if ($request->has('inventory')) {
            $syncData = [];
            foreach ($request->inventory as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity'] ?? 1];
            }
            $jobcard->inventory()->sync($syncData);
        }
        return response()->json(['success' => true, 'jobcard' => $jobcard->fresh(['client', 'inventory', 'employees'])]);
    }

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
        DB::transaction(function () use ($request) {
            $jobcard = Jobcard::create($request->only([
                'jobcard_number', 'job_date', 'client_id', 'category', 'work_request', 'special_request', 
                'status', 'work_done', 'time_spent',
                // Add the new hour fields
                'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
                'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
            ]));

            // Sync employees (with optional hours)
            if ($request->has('employee_hours')) {
                $syncData = [];
                foreach ($request->employee_hours as $employeeId => $hours) {
                    $syncData[$employeeId] = ['hours_worked' => $hours];
                }
                $jobcard->employees()->sync($syncData);
            } elseif ($request->has('employees')) {
                $jobcard->employees()->sync($request->employees);
            }

            // Sync inventory (with quantities) and update stock
            if ($request->has('inventory_qty')) {
                $syncData = [];
                foreach ($request->inventory_qty as $itemId => $qty) {
                    $syncData[$itemId] = ['quantity' => $qty];
                    $inventory = Inventory::find($itemId);
                    if ($inventory) {
                        $inventory->stock_level = max(0, $inventory->stock_level - $qty);
                        $inventory->save();
                    }
                }
                $jobcard->inventory()->sync($syncData);
            }
        });

        return redirect()->route('jobcard.index')->with('success', 'Jobcard created!');
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

    public function editMobile($id)
    {
        $jobcard = Jobcard::with(['client', 'employees', 'inventory'])->findOrFail($id);
        $employees = Employee::all();
        $inventory = Inventory::all();
        $clients = Client::all();
        $assignedInventory = $jobcard->inventory->map(function($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->pivot->quantity ?? 1,
                'name' => $item->name ?? $item->description
            ];
        })->toArray();
        return view('mobile app.jobcard-editor-mobile', compact('jobcard', 'employees', 'inventory', 'clients', 'assignedInventory'));
    }

    public function update(Request $request, Jobcard $jobcard)
    {
        DB::transaction(function () use ($request, $jobcard) {
            // Update jobcard basic fields
            $jobcard->update($request->only([
                'jobcard_number', 'job_date', 'client_id', 'category', 'work_request', 'special_request', 
                'status', 'work_done', 'time_spent', 'progress_note',
                'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
                'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
            ]));

            // Handle employees with hours AND hour types
            if ($request->has('employees')) {
                $syncData = [];
                foreach ($request->employees as $employeeId) {
                    $hours = $request->employee_hours[$employeeId] ?? 0;
                    $hourType = $request->employee_hour_types[$employeeId] ?? 'normal';
                    
                    $syncData[$employeeId] = [
                        'hours_worked' => $hours,
                        'hour_type' => $hourType  // Add this to pivot table
                    ];
                }
                $jobcard->employees()->sync($syncData);
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
        });

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
                'labour_rate' => number_format($company->labour_rate, 2),
                'overtime_rate' => number_format($company->labour_rate * $company->overtime_multiplier, 2),
                'weekend_rate' => number_format($company->labour_rate * $company->weekend_multiplier, 2),
                'holiday_rate' => number_format($company->labour_rate * $company->public_holiday_multiplier, 2),
                'mileage_rate' => number_format($company->mileage_rate, 2),
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
