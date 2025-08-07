<?php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Traits\TenantDatabaseSwitch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JobcardController extends Controller
{
    use TenantDatabaseSwitch;
    
    /**
     * Show the form for creating a new jobcard (mobile version).
     */
    public function createMobile()
    {
        // Switch to tenant database for mobile user
        $this->switchToTenantDatabase();
        
        $employees = \App\Models\Employee::all();
        $inventory = \App\Models\Inventory::all();
        $clients = \App\Models\Client::all();
        // Jobcard number will be generated dynamically based on category selection
        return view('mobile.jobcard-create', compact('employees', 'inventory', 'clients'));
    }
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
                $inventory = Inventory::find($item['id']);
                $syncData[$item['id']] = [
                    'quantity' => $item['quantity'] ?? 1,
                    'buying_price' => $inventory ? $inventory->buying_price : null,
                    'selling_price' => $inventory ? $inventory->selling_price : null,
                ];
            }
            $jobcard->inventory()->sync($syncData);
        }
        return response()->json(['success' => true, 'jobcard' => $jobcard->fresh(['client', 'inventory', 'employees'])]);
    }

    public function index(Request $request)
    {
        // Switch to tenant database
        $this->switchToTenantDatabase();
        
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
        // Only switch to tenant database if user is in tenant context
        if (session('tenant_database') && session('tenant_database') !== 'main') {
            $this->switchToTenantDatabase();
            // Re-fetch the jobcard from the correct tenant database
            $jobcard = Jobcard::findOrFail($jobcard->id);
        }
        
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
        // Only switch to tenant database if user is in tenant context
        if (session('tenant_database') && session('tenant_database') !== 'main') {
            $this->switchToTenantDatabase();
        }
        
        $employees = Employee::all();
        $inventory = Inventory::all();
        return view('jobcard.create', compact('employees', 'inventory'));
    }

    public function store(Request $request)
    {
        try {
            // Basic validation
            $request->validate([
                'client_id' => 'required',
                'category' => 'required|string',
                'job_date' => 'required|date',
                'work_request' => 'nullable|string',
                'special_request' => 'nullable|string',
                'photos.*' => 'nullable|image|max:15360' // 15MB max per photo
            ]);
            
            // Additional validation for mobile
            if ($request->session()->get('mobile_employee_id')) {
                $request->validate([
                    'jobcard_number' => 'required|string'
                ]);
            }
            
            // Switch to tenant database (handles both admin and mobile users)
            $this->switchToTenantDatabase();

            DB::transaction(function () use ($request) {
                $mobileEmployeeId = $request->session()->get('mobile_employee_id');
                $clientId = $request->input('client_id');
                
                // Log the incoming data for debugging
                Log::info('Creating jobcard with data:', [
                    'client_id' => $clientId,
                    'category' => $request->input('category'),
                    'mobile_employee_id' => $mobileEmployeeId,
                    'tenant_database' => session('tenant_database')
                ]);
                
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
            
            // Ensure status has a default value if null or empty
            if (empty($data['status'])) {
                $data['status'] = 'assigned';
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

            // Ensure required fields are present
            if (empty($clientId)) {
                throw new \Exception('Client ID is required but missing');
            }
            if (empty($data['category'])) {
                throw new \Exception('Category is required but missing');
            }
            if (empty($data['job_date'])) {
                throw new \Exception('Job date is required but missing');
            }
            if (empty($data['jobcard_number'])) {
                throw new \Exception('Jobcard number is required but missing');
            }

            // Log the data being used to create the jobcard
            Log::info('Creating jobcard with data:', [
                'data' => array_merge($data, ['client_id' => $clientId]),
                'tenant_database' => session('tenant_database'),
                'mobile_employee_id' => $mobileEmployeeId
            ]);

            $jobcard = Jobcard::create(array_merge($data, ['client_id' => $clientId]));
            
            // Log successful creation
            Log::info('Jobcard created successfully:', [
                'jobcard_id' => $jobcard->id,
                'jobcard_number' => $jobcard->jobcard_number
            ]);

            // --- EMPLOYEES ---
            Log::info('Processing employees data:', [
                'has_employees' => $request->has('employees'),
                'employees_data' => $request->input('employees', []),
                'mobile_employee_id' => $mobileEmployeeId
            ]);
            
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
            // Always link the logged-in mobile employee
            if ($mobileEmployeeId && !isset($syncData[$mobileEmployeeId])) {
                $syncData[$mobileEmployeeId] = [
                    'hours_worked' => 0,
                    'hour_type' => 'normal',
                ];
            }
            
            Log::info('Employee sync data:', ['syncData' => $syncData]);
            
            if (!empty($syncData)) {
                $jobcard->employees()->sync($syncData);
                Log::info('Employee sync completed');
            }

            // --- TRAVELING ---
            Log::info('Processing traveling data:', [
                'has_traveling' => $request->has('traveling'),
                'traveling_data' => $request->input('traveling', [])
            ]);
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
            
            // Handle photo uploads for mobile jobcard creation
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('jobcards/' . $jobcard->id, 'public');
                    \App\Models\MobileJobcardPhoto::create([
                        'jobcard_id' => $jobcard->id,
                        'file_path' => $path,
                        'uploaded_at' => now(),
                        'uploaded_by' => $mobileEmployeeId ?? Auth::id(),
                    ]);
                }
            }
        });

        Log::info('Jobcard creation completed, redirecting to mobile index');
        
        // Redirect to mobile jobcard list after creation
        return redirect()->route('mobile.jobcards.index')->with('success', 'Jobcard created!');
        
        } catch (\Exception $e) {
            Log::error('Error creating jobcard: ' . $e->getMessage(), [
                'request_data' => $request->except(['_token', 'photos']), // Exclude large photo data from logs
                'trace' => $e->getTraceAsString(),
                'tenant_database' => session('tenant_database'),
                'mobile_employee_id' => $request->session()->get('mobile_employee_id')
            ]);
            
            if ($request->session()->get('mobile_employee_id')) {
                return redirect()->back()->withErrors(['error' => 'Error saving jobcard. Please try again.'])->withInput();
            } else {
                return redirect()->back()->withErrors(['error' => 'Error saving jobcard: ' . $e->getMessage()])->withInput();
            }
        }
    }

    public function edit(Jobcard $jobcard)
    {
        // Only switch to tenant database if user is in tenant context
        if (session('tenant_database') && session('tenant_database') !== 'main') {
            $this->switchToTenantDatabase();
            // Re-fetch the jobcard from the correct tenant database
            $jobcard = Jobcard::findOrFail($jobcard->id);
        }
        
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
        // Switch to tenant database for mobile user
        $this->switchToTenantDatabase();
        
        $jobcard = Jobcard::with(['client', 'inventory', 'mobilePhotos'])->findOrFail($id);
        $inventory = Inventory::all();
        $assignedInventory = $jobcard->inventory->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->description ?? $item->name ?? '',
                'quantity' => $item->pivot->quantity ?? 1,
            ];
        });

        // Fetch all employees
        $employees = \App\Models\Employee::all();

        return view('mobile.jobcard-edit', [
            'jobcard' => $jobcard,
            'inventory' => $inventory,
            'assignedInventory' => $assignedInventory,
            'employees' => $employees,
        ]);
    }

    public function showMobile($id)
    {
        // Switch to tenant database for mobile user
        $this->switchToTenantDatabase();
        
        $jobcard = Jobcard::with('client', 'inventory')->findOrFail($id);
        return view('mobile.jobcard-view', compact('jobcard'));
    }

    public function update(Request $request, Jobcard $jobcard)
    {
        // Only switch to tenant database if user is in tenant context
        if (session('tenant_database') && session('tenant_database') !== 'main') {
            $this->switchToTenantDatabase();
            // Re-fetch the jobcard from the correct tenant database
            $jobcard = Jobcard::findOrFail($jobcard->id);
        }
        
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

    public function mobileIndex(Request $request)
    {
        // Switch to tenant database for mobile user
        $this->switchToTenantDatabase();
        
        // Require mobile employee login
        $employeeId = $request->session()->get('mobile_employee_id');
        if (!$employeeId) {
            return redirect('/mobile-app/login');
        }
        // Only show jobcards assigned to this employee that are visible on mobile
        $jobcards = \App\Models\Jobcard::with('client')
            ->whereHas('employees', function($q) use ($employeeId) {
                $q->where('employees.id', $employeeId);
            })
            ->where('visible_on_mobile', true) // Only show mobile-visible jobcards
            ->whereNotIn('status', ['invoiced']) // Remove completed from exclusion since they can still be worked on
            ->orderBy('job_date', 'desc')
            ->paginate(15);
        return view('mobile.jobcard-list', compact('jobcards'));
    }

    public function acceptQuote(Request $request, Jobcard $jobcard)
    {
        if (!$jobcard->is_quote || $jobcard->quote_accepted_at) {
            return redirect()->back()->with('error', 'This quote cannot be accepted.');
        }
        
        $request->validate([
            'accepted_signature' => 'required|string|max:255',
        ]);
        
        $jobcard->update([
            'quote_accepted_at' => now(),
            'accepted_by' => $request->user()->id,
            'accepted_signature' => $request->accepted_signature,
            'is_quote' => false, // Now becomes a jobcard
        ]);
        
        return redirect()->route('jobcard.edit', $jobcard->id)->with('success', 'Quote accepted and converted to jobcard!');
    }

    /**
     * Remove completed jobcard from mobile view (doesn't delete the jobcard)
     */
    public function removeFromMobile($id)
    {
        $jobcard = Jobcard::findOrFail($id);
        
        // Only allow removing completed jobcards from mobile
        if ($jobcard->status !== 'completed') {
            return response()->json(['error' => 'Only completed jobcards can be removed from mobile view'], 400);
        }
        
        $jobcard->update(['visible_on_mobile' => false]);
        
        return response()->json(['success' => 'Jobcard removed from mobile view']);
    }
    
    /**
     * Update jobcard from mobile interface
     */
    public function mobileUpdate(Request $request, $id)
    {
        try {
            // Switch to tenant database for mobile user
            $this->switchToTenantDatabase();
            
            // Find the jobcard in the tenant database
            $jobcard = Jobcard::findOrFail($id);
            
            // Update the jobcard with the provided data
            $jobcard->update($request->only([
                'jobcard_number', 'job_date', 'client_id', 'category', 
                'work_request', 'special_request', 'status', 'work_done',
                'normal_hours', 'overtime_hours', 'weekend_hours', 
                'public_holiday_hours', 'call_out_hours', 'total_labour_cost',
                'is_quote'
            ]));
            
            return response()->json(['success' => true, 'message' => 'Jobcard updated successfully!']);
            
        } catch (\Exception $e) {
            Log::error('Error updating mobile jobcard: ' . $e->getMessage(), [
                'jobcard_id' => $id,
                'request_data' => $request->except(['_token']),
                'tenant_database' => session('tenant_database'),
                'mobile_employee_id' => $request->session()->get('mobile_employee_id')
            ]);
            
            return response()->json(['success' => false, 'message' => 'Error updating jobcard: ' . $e->getMessage()], 500);
        }
    }
}
