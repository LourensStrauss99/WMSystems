@extends('layouts.mobile')

@section('content')
<div class="container-fluid px-2 py-2">
    <!-- Jobcard Header -->
    <div class="card mb-3 shadow-sm border-0" style="background: #1976d2; color: #fff;">
        <div class="card-body py-2 px-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="fas fa-clipboard-list me-2"></i>Jobcard #{{ $jobcard->jobcard_number }}
                </h5>
                <div class="small">{{ $clients->find($jobcard->client_id)->name ?? '' }}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2">{{ ucfirst($jobcard->status) }}</span>
                <a href="/mobile-app/jobcard/index" class="btn btn-light btn-sm ms-2"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </div>
    </div>

    <form id="jobcardForm" method="POST" action="{{ route('mobile.jobcard.update', $jobcard->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-12 px-0">
                <!-- Job Information -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-header py-2 px-3" style="background:#1565c0; color:#fff;">
                        <strong><i class="fas fa-info-circle me-2"></i>Job Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="mb-2"><span class="fw-bold">Date:</span> {{ $jobcard->job_date }}</div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2"><span class="fw-bold">Category:</span> {{ $jobcard->category }}</div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold">Work Request:</span><br>
                            <span>{{ $jobcard->work_request }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold">Special Instructions:</span><br>
                            <span class="text-danger">{{ $jobcard->special_request }}</span>
                        </div>
                    </div>
                </div>

                <!-- Client Details -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-header py-2 px-3" style="background:#039be5; color:#fff;">
                        <strong><i class="fas fa-user me-2"></i>Client Details</strong>
                    </div>
                    <div class="card-body">
                        @php $client = $clients->find($jobcard->client_id); @endphp
                        <div class="mb-1 fw-bold">{{ $client->name ?? '' }}</div>
                        <div class="mb-1 text-muted small"><i class="fas fa-map-marker-alt me-1"></i>{{ $client->address ?? '' }}</div>
                        <div class="mb-1 text-muted small"><i class="fas fa-envelope me-1"></i>{{ $client->email ?? '' }}</div>
                        <a href="tel:{{ $client->phone ?? '' }}" class="btn btn-success btn-sm mt-2"><i class="fas fa-phone"></i></a>
                    </div>
                </div>

                <!-- Assigned Team -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-header py-2 px-3" style="background:#3949ab; color:#fff;">
                        <strong><i class="fas fa-users me-2"></i>Assigned Team</strong>
                    </div>
                    <div class="card-body">
                        @foreach($jobcard->employees as $employee)
                            <span class="badge bg-secondary me-2">{{ $employee->name }}</span>
                        @endforeach
                    </div>
                </div>

                <!-- Materials Required -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-header py-2 px-3" style="background:#212121; color:#fff;">
                        <strong><i class="fas fa-boxes me-2"></i>Materials Required</strong>
                    </div>
                    <div class="card-body">
                        @forelse($assignedInventory as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $item['name'] }}</span>
                                <span class="badge bg-warning text-dark">Qty: {{ $item['quantity'] }}</span>
                            </div>
                        @empty
                            <div class="text-muted">No materials required</div>
                        @endforelse
                    </div>
                </div>

                <!-- Work Progress -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-header py-2 px-3" style="background:#388e3c; color:#fff;">
                        <strong><i class="fas fa-tasks me-2"></i>Work Progress</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Work Completed:</label>
                            <textarea name="work_done" class="form-control" rows="3" placeholder="Describe the work completed...">{{ old('work_done', $jobcard->work_done) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Additional Notes:</label>
                            <textarea name="additional_notes" class="form-control" rows="2" placeholder="Any additional notes or observations..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Time Spent (hours):</label>
                            <input type="number" name="time_spent" class="form-control" min="0" step="0.1" value="{{ old('time_spent', 0) }}">
                        </div>
                        <!-- Enhanced Photos Section -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Photos:</label>
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-outline-primary flex-fill" onclick="document.getElementById('takePhoto').click()"><i class="fas fa-camera"></i> Take Photo</button>
                                <button type="button" class="btn btn-outline-secondary flex-fill" onclick="document.getElementById('pickGallery').click()"><i class="fas fa-images"></i> Gallery</button>
                            </div>
                            <input type="file" id="takePhoto" name="photos[]" accept="image/*" capture="environment" style="display:none" onchange="handleImageSelect(event)" multiple>
                            <input type="file" id="pickGallery" name="photos[]" accept="image/*" style="display:none" onchange="handleImageSelect(event)" multiple>
                            <div id="imagePreview" class="d-flex flex-wrap mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Job Completion -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-header py-2 px-3" style="background:#00897b; color:#fff;">
                        <strong><i class="fas fa-check-circle me-2"></i>Job Completion</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="job_completed" id="job_completed">
                            <label class="form-check-label fw-bold" for="job_completed">Mark this job as completed</label>
                        </div>
                    </div>
                </div>

                <!-- Save Progress Button -->
                <div class="d-grid gap-2 mb-4">
                    <button type="button" class="btn btn-primary btn-lg" onclick="saveLocally()">
                        <i class="fas fa-save me-2"></i>Save Progress
                    </button>
                    <button type="button" class="btn btn-success btn-lg" onclick="submitToOffice()">
                        <i class="fas fa-paper-plane me-2"></i>Submit to Office
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Employees Card with Integrated Hour Types -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users text-success me-2"></i>Assigned Employees & Hours
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">Employee</label>
                        <select id="employee_select" class="form-control">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted">Hours Worked</label>
                        <input type="number" id="employee_hours_input" class="form-control" 
                               min="0" step="0.5" placeholder="Hours">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted">Hour Type</label>
                        <select id="hour_type_select" class="form-control">
                            <option value="normal">Normal (R<span class="normal-rate">750</span>/hr)</option>
                            <option value="overtime">Overtime (R<span class="overtime-rate">1,125</span>/hr)</option>
                            <option value="weekend">Weekend (R<span class="weekend-rate">1,500</span>/hr)</option>
                            <option value="holiday">Holiday (R<span class="holiday-rate">1,875</span>/hr)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted">&nbsp;</label>
                        <button type="button" class="btn btn-success text-white d-block w-100" onclick="addEmployee()">
                            <i class="fas fa-plus me-1"></i>Add
                        </button>
                    </div>
                </div>

                <!-- Call Out & Mileage Section (moved here for better flow) -->
                <div class="row g-3 mb-3 border-top pt-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">Call Out Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">R</span>
                            <input type="number" name="call_out_fee" id="call_out_fee" class="form-control" 
                                   min="0" step="0.01" value="{{ old('call_out_fee', $jobcard->call_out_fee ?? 0) }}" 
                                   onchange="calculateTotalCosts()">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="setStandardCallOut()">
                            <i class="fas fa-phone me-1"></i>Standard (R1,000)
                        </button>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">Mileage (km)</label>
                        <input type="number" name="mileage_km" id="mileage_km" class="form-control" 
                               min="0" step="0.1" value="{{ old('mileage_km', $jobcard->mileage_km ?? 0) }}" 
                               onchange="calculateTotalCosts()">
                        <small class="text-muted">Rate: R<span id="mileage_rate">7.50</span>/km</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">Mileage Cost</label>
                        <div class="input-group">
                            <span class="input-group-text">R</span>
                            <input type="number" name="mileage_cost" id="mileage_cost" class="form-control" 
                                   readonly value="{{ old('mileage_cost', $jobcard->mileage_cost ?? 0) }}">
                        </div>
                        <small class="text-muted">Auto-calculated</small>
                    </div>
                </div>

                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">
                            <i class="fas fa-list me-2"></i>Current Employees & Hours
                        </h6>
                        <div class="text-end">
                            <small class="text-muted">Total Labour Cost: </small>
                            <strong class="text-success">R<span id="display_total_labour_cost">0.00</span></strong>
                        </div>
                    </div>
                    <ul id="employee_list" class="list-unstyled mb-0">
                        @forelse($jobcard->employees as $employee)
                            @php
                                $hourType = $employee->pivot->hour_type ?? 'normal';
                                $hours = $employee->pivot->hours_worked ?? 0;
                                $company = \App\Models\CompanyDetail::first();
                                
                                // Calculate rate based on hour type
                                $baseRate = $company->labour_rate ?? 750;
                                switch($hourType) {
                                    case 'overtime':
                                        $rate = $baseRate * ($company->overtime_multiplier ?? 1.5);
                                        $badgeClass = 'bg-warning text-dark';
                                        $label = 'Overtime';
                                        break;
                                    case 'weekend':
                                        $rate = $baseRate * ($company->weekend_multiplier ?? 2.0);
                                        $badgeClass = 'bg-primary';
                                        $label = 'Weekend';
                                        break;
                                    case 'holiday':
                                        $rate = $baseRate * ($company->public_holiday_multiplier ?? 2.5);
                                        $badgeClass = 'bg-danger';
                                        $label = 'Holiday';
                                        break;
                                    default:
                                        $rate = $baseRate;
                                        $badgeClass = 'bg-secondary';
                                        $label = 'Normal';
                                }
                                $cost = $hours * $rate;
                            @endphp
                            <li data-id="{{ $employee->id }}" data-hours="{{ $hours }}" data-type="{{ $hourType }}" 
                                class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>{{ $employee->name }}</strong>
                                    <span class="badge bg-info ms-2">{{ $hours }} hrs</span>
                                    <span class="badge {{ $badgeClass }} ms-1">{{ $label }}</span>
                                    <small class="text-muted ms-2">R{{ number_format($cost, 2) }}</small>
                                </div>
                                <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                                <input type="hidden" name="employee_hours[{{ $employee->id }}]" value="{{ $hours }}">
                                <input type="hidden" name="employee_hour_types[{{ $employee->id }}]" value="{{ $hourType }}">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        @empty
                            <li class="text-muted">No employees assigned yet</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Hidden fields for hour totals (for form submission) -->
                <input type="hidden" name="normal_hours" id="hidden_normal_hours" value="{{ old('normal_hours', $jobcard->normal_hours ?? 0) }}">
                <input type="hidden" name="overtime_hours" id="hidden_overtime_hours" value="{{ old('overtime_hours', $jobcard->overtime_hours ?? 0) }}">
                <input type="hidden" name="weekend_hours" id="hidden_weekend_hours" value="{{ old('weekend_hours', $jobcard->weekend_hours ?? 0) }}">
                <input type="hidden" name="public_holiday_hours" id="hidden_public_holiday_hours" value="{{ old('public_holiday_hours', $jobcard->public_holiday_hours ?? 0) }}">
                <input type="hidden" name="total_labour_cost" id="hidden_total_labour_cost" value="{{ old('total_labour_cost', $jobcard->total_labour_cost ?? 0) }}">
            </div>
        </div>

        <!-- Inventory Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-boxes text-warning me-2"></i>Inventory Items
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Inventory Item</label>
                        <select id="inventory_select" class="form-control">
                            <option value="">Select Inventory Item</option>
                            @foreach($inventory as $item)
                                @php $stockStatus = $item->getStockStatus(); @endphp
                                <option value="{{ $item->id }}" 
                                        data-short="{{ $item->short_description }}"
                                        data-stock="{{ $item->stock_level }}"
                                        data-min="{{ $item->min_level }}"
                                        data-code="{{ $item->short_code }}"
                                        data-status="{{ $stockStatus['status'] }}">
                                    [{{ $item->short_code }}] {{ $item->name }} (Stock: {{ $item->stock_level }}) {{ $stockStatus['icon'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted">Quantity</label>
                        <input type="number" id="inventory_quantity" class="form-control" 
                               min="1" max="100" value="1" placeholder="Qty">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted">&nbsp;</label>
                        <button type="button" id="add_inventory" class="btn btn-primary text-white d-block">
                            <i class="fas fa-plus me-2"></i>Add Item
                        </button>
                    </div>
                </div>

                <div class="border rounded p-3 bg-light">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-list me-2"></i>Current Items
                    </h6>
                    <ul id="inventory_list" class="list-unstyled mb-0">
                        @forelse($assignedInventory as $item)
                            <li data-id="{{ $item['id'] }}" class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>{{ $item['name'] }}</strong>
                                    <span class="badge bg-warning text-dark ms-2">Qty: {{ $item['quantity'] }}</span>
                                </div>
                                <input type="hidden" name="inventory_items[]" value="{{ $item['id'] }}">
                                <input type="hidden" name="inventory_qty[{{ $item['id'] }}]" value="{{ $item['quantity'] }}">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInventory(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        @empty
                            <li class="text-muted">No inventory items added yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Status & Actions Card (moved from sidebar) -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks text-info me-2"></i>Status & Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted">Current Status</label>
                    <select name="status" class="form-control" required>
                        <option value="assigned" {{ $jobcard->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in progress" {{ $jobcard->status == 'in progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $jobcard->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="invoiced" {{ $jobcard->status == 'invoiced' ? 'selected' : '' }}>Invoiced</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success text-white" onclick="submitToOffice()">
                        <i class="fas fa-paper-plane me-2"></i>Submit to Office
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="saveLocally()">
                        <i class="fas fa-save me-2"></i>Save Locally
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Card (moved from sidebar) -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-sm mb-2">
                    <div class="col-7"><i class="fas fa-calendar text-muted me-2"></i>Created:</div>
                    <div class="col-5 text-end">{{ $jobcard->created_at->format('d M Y') }}</div>
                </div>
                <div class="row text-sm mb-2">
                    <div class="col-7"><i class="fas fa-users text-muted me-2"></i>Employees:</div>
                    <div class="col-5 text-end fw-bold" id="employee_count">{{ $jobcard->employees->count() }}</div>
                </div>
                <div class="row text-sm mb-2">
                    <div class="col-7"><i class="fas fa-boxes text-muted me-2"></i>Items:</div>
                    <div class="col-5 text-end fw-bold" id="inventory_count">{{ $jobcard->inventory->count() }}</div>
                </div>
                <div class="row text-sm">
                    <div class="col-7"><i class="fas fa-clock text-muted me-2"></i>Total Hours:</div>
                    <div class="col-5 text-end fw-bold" id="total_hours">{{ $jobcard->employees->sum('pivot.hours_worked') }}</div>
                </div>
            </div>
        </div>
    </form>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
// Local save and submit logic
const LOCAL_KEY = 'jobcard_{{ $jobcard->id }}_draft';

function getFormData() {
    const form = document.getElementById('jobcardForm');
    const data = {};
    form.querySelectorAll('input, select, textarea').forEach(el => {
        if (el.name) {
            if (el.type === 'checkbox') {
                data[el.name] = el.checked;
            } else {
                data[el.name] = el.value;
            }
        }
    });
    // Employees
    data.employees = [];
    document.querySelectorAll('#employee_list li[data-id]').forEach(li => {
        data.employees.push({
            id: li.getAttribute('data-id'),
            hours: li.getAttribute('data-hours'),
            type: li.getAttribute('data-type')
        });
    });
    // Inventory
    data.inventory = [];
    document.querySelectorAll('#inventory_list li[data-id]').forEach(li => {
        data.inventory.push({
            id: li.getAttribute('data-id'),
            qty: li.querySelector('input[name^="inventory_qty"]')?.value || 1
        });
    });
    return data;
}

function saveLocally() {
    const data = getFormData();
    localStorage.setItem(LOCAL_KEY, JSON.stringify(data));
    showToast('Saved locally on device.', 'success');
}

function loadLocalDraft() {
    const draft = localStorage.getItem(LOCAL_KEY);
    if (!draft) return;
    try {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            if (key === 'employees' || key === 'inventory') return;
            const el = document.querySelector(`[name='${key}']`);
            if (el) el.value = data[key];
        });
        // Restore employees
        if (Array.isArray(data.employees)) {
            document.getElementById('employee_list').innerHTML = '';
            data.employees.forEach(emp => {
                let select = document.getElementById('employee_select');
                let name = select.querySelector(`option[value='${emp.id}']`)?.text || 'Employee';
                let hourTypeLabel = 'Normal', badgeClass = 'bg-secondary', rate = 750;
                switch(emp.type) {
                    case 'overtime': rate = 750 * 1.5; hourTypeLabel = 'Overtime'; badgeClass = 'bg-warning text-dark'; break;
                    case 'weekend': rate = 750 * 2.0; hourTypeLabel = 'Weekend'; badgeClass = 'bg-primary'; break;
                    case 'holiday': rate = 750 * 2.5; hourTypeLabel = 'Holiday'; badgeClass = 'bg-danger'; break;
                }
                const cost = emp.hours * rate;
                let li = document.createElement('li');
                li.setAttribute('data-id', emp.id);
                li.setAttribute('data-hours', emp.hours);
                li.setAttribute('data-type', emp.type);
                li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
                li.innerHTML = `<div><strong>${name}</strong><span class='badge bg-info ms-2'>${emp.hours} hrs</span><span class='badge ${badgeClass} ms-1'>${hourTypeLabel}</span><small class='text-muted ms-2'>R${cost.toFixed(2)}</small></div><input type='hidden' name='employees[]' value='${emp.id}'><input type='hidden' name='employee_hours[${emp.id}]' value='${emp.hours}'><input type='hidden' name='employee_hour_types[${emp.id}]' value='${emp.type}'><button type='button' class='btn btn-sm btn-outline-danger' onclick='removeEmployee(this)'><i class='fas fa-times'></i></button>`;
                document.getElementById('employee_list').appendChild(li);
            });
        }
        // Restore inventory
        if (Array.isArray(data.inventory)) {
            document.getElementById('inventory_list').innerHTML = '';
            data.inventory.forEach(item => {
                let select = document.getElementById('inventory_select');
                let name = select.querySelector(`option[value='${item.id}']`)?.text || 'Item';
                let li = document.createElement('li');
                li.setAttribute('data-id', item.id);
                li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
                li.innerHTML = `<div><strong>${name.split(' (Stock:')[0]}</strong><span class='badge bg-warning text-dark ms-2'>Qty: ${item.qty}</span></div><input type='hidden' name='inventory_items[]' value='${item.id}'><input type='hidden' name='inventory_qty[${item.id}]' value='${item.qty}'><button type='button' class='btn btn-sm btn-outline-danger' onclick='removeInventory(this)'><i class='fas fa-times'></i></button>`;
                document.getElementById('inventory_list').appendChild(li);
            });
        }
        updateHourTotals();
        updateSummary();
        showToast('Draft loaded from device.', 'info');
    } catch(e) { showToast('Failed to load draft.', 'danger'); }
}

function submitToOffice() {
    const form = document.getElementById('jobcardForm');
    const formData = new FormData(form);
    const draft = getFormData();
    formData.delete('employees[]');
    formData.delete('inventory_items[]');
    draft.employees.forEach(emp => {
        formData.append('employees[]', emp.id);
        formData.append(`employee_hours[${emp.id}]`, emp.hours);
        formData.append(`employee_hour_types[${emp.id}]`, emp.type);
    });
    draft.inventory.forEach(item => {
        formData.append('inventory_items[]', item.id);
        formData.append(`inventory_qty[${item.id}]`, item.qty);
    });
    formData.append('_method', 'PUT');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    fetch("{{ route('jobcard.update', $jobcard->id) }}", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json().catch(() => res.text()))
    .then(resp => {
        if ((resp && resp.success) || (resp && resp.status === 'success')) {
            showToast('Submitted to office!', 'success');
            localStorage.removeItem(LOCAL_KEY);
        } else {
            showToast('Submission failed.', 'danger');
        }
    })
    .catch(() => showToast('Network error.', 'danger'));
}

function showToast(msg, type) {
    let toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.style.zIndex = 9999;
    toast.innerHTML = `<div class='d-flex'><div class='toast-body'>${msg}</div><button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast'></button></div>`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 3000);
}

function addInventoryToJobcard() {
    let select = document.getElementById('inventory_select');
    let qty = document.getElementById('inventory_quantity').value;
    let id = select.value;
    let name = select.options[select.selectedIndex].text;
    if (!id || !qty || qty < 1) {
        alert('Please select an item and enter valid quantity');
        return;
    }
    if (document.querySelector('#inventory_list li[data-id="'+id+'"]')) {
        alert('This item is already added to the jobcard');
        return;
    }
    const noItemsMsg = document.querySelector('#inventory_list .text-muted');
    if (noItemsMsg && noItemsMsg.textContent.includes('No inventory')) {
        noItemsMsg.remove();
    }
    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
    li.innerHTML = `<div><strong>${name.split(' (Stock:')[0]}</strong><span class='badge bg-warning text-dark ms-2'>Qty: ${qty}</span></div><input type='hidden' name='inventory_items[]' value='${id}'><input type='hidden' name='inventory_qty[${id}]' value='${qty}'><button type='button' class='btn btn-sm btn-outline-danger' onclick='removeInventory(this)'><i class='fas fa-times'></i></button>`;
    document.getElementById('inventory_list').appendChild(li);
    select.value = '';
    document.getElementById('inventory_quantity').value = 1;
    updateSummary();
}

function removeInventory(btn) {
    btn.closest('li').remove();
    const list = document.getElementById('inventory_list');
    if (list.children.length === 0) {
        list.innerHTML = '<li class="text-muted">No inventory items added yet</li>';
    }
    updateSummary();
}

function addEmployee() {
    let select = document.getElementById('employee_select');
    let hours = parseFloat(document.getElementById('employee_hours_input').value) || 0;
    let hourType = document.getElementById('hour_type_select').value;
    let id = select.value;
    let name = select.options[select.selectedIndex].text;
    if (!id || hours <= 0) {
        alert('Please select an employee and enter valid hours');
        return;
    }
    if (document.querySelector('#employee_list li[data-id="'+id+'"]')) {
        alert('This employee is already assigned');
        return;
    }
    const noEmployeesMsg = document.querySelector('#employee_list .text-muted');
    if (noEmployeesMsg && noEmployeesMsg.textContent.includes('No employees')) {
        noEmployeesMsg.remove();
    }
    let rate = 750;
    let hourTypeLabel = 'Normal';
    let badgeClass = 'bg-secondary';
    switch(hourType) {
        case 'overtime': rate = 750 * 1.5; hourTypeLabel = 'Overtime'; badgeClass = 'bg-warning text-dark'; break;
        case 'weekend': rate = 750 * 2.0; hourTypeLabel = 'Weekend'; badgeClass = 'bg-primary'; break;
        case 'holiday': rate = 750 * 2.5; hourTypeLabel = 'Holiday'; badgeClass = 'bg-danger'; break;
    }
    const cost = hours * rate;
    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.setAttribute('data-hours', hours);
    li.setAttribute('data-type', hourType);
    li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
    li.innerHTML = `<div><strong>${name}</strong><span class='badge bg-info ms-2'>${hours} hrs</span><span class='badge ${badgeClass} ms-1'>${hourTypeLabel}</span><small class='text-muted ms-2'>R${cost.toFixed(2)}</small></div><input type='hidden' name='employees[]' value='${id}'><input type='hidden' name='employee_hours[${id}]' value='${hours}'><input type='hidden' name='employee_hour_types[${id}]' value='${hourType}'><button type='button' class='btn btn-sm btn-outline-danger' onclick='removeEmployee(this)'><i class='fas fa-times'></i></button>`;
    document.getElementById('employee_list').appendChild(li);
    select.value = '';
    document.getElementById('employee_hours_input').value = '';
    document.getElementById('hour_type_select').value = 'normal';
    updateHourTotals();
    updateSummary();
}

function removeEmployee(btn) {
    btn.closest('li').remove();
    const list = document.getElementById('employee_list');
    if (list.children.length === 0) {
        list.innerHTML = '<li class="text-muted">No employees assigned yet</li>';
    }
    updateHourTotals();
    updateSummary();
}

function updateHourTotals() {
    let normalHours = 0, overtimeHours = 0, weekendHours = 0, holidayHours = 0;
    let totalLabourCost = 0;
    document.querySelectorAll('#employee_list li[data-id]').forEach(function(li) {
        const hours = parseFloat(li.getAttribute('data-hours')) || 0;
        const type = li.getAttribute('data-type') || 'normal';
        let rate = 750;
        switch(type) {
            case 'normal': normalHours += hours; rate = 750; break;
            case 'overtime': overtimeHours += hours; rate = 750 * 1.5; break;
            case 'weekend': weekendHours += hours; rate = 750 * 2.0; break;
            case 'holiday': holidayHours += hours; rate = 750 * 2.5; break;
        }
        totalLabourCost += hours * rate;
    });
    const callOutFee = parseFloat(document.getElementById('call_out_fee').value) || 0;
    const mileageKm = parseFloat(document.getElementById('mileage_km').value) || 0;
    const mileageCost = mileageKm * 7.5;
    totalLabourCost += callOutFee + mileageCost;
    document.getElementById('hidden_normal_hours').value = normalHours.toFixed(2);
    document.getElementById('hidden_overtime_hours').value = overtimeHours.toFixed(2);
    document.getElementById('hidden_weekend_hours').value = weekendHours.toFixed(2);
    document.getElementById('hidden_public_holiday_hours').value = holidayHours.toFixed(2);
    document.getElementById('hidden_total_labour_cost').value = totalLabourCost.toFixed(2);
    document.getElementById('mileage_cost').value = mileageCost.toFixed(2);
    document.getElementById('display_total_labour_cost').textContent = totalLabourCost.toFixed(2);
}

function calculateTotalCosts() {
    updateHourTotals();
}

function setStandardCallOut() {
    document.getElementById('call_out_fee').value = 1000;
    updateHourTotals();
}

function updateSummary() {
    const employeeCount = document.querySelectorAll('#employee_list li[data-id]').length;
    if (document.getElementById('employee_count')) {
        document.getElementById('employee_count').textContent = employeeCount;
    }
    const inventoryCount = document.querySelectorAll('#inventory_list li[data-id]').length;
    if (document.getElementById('inventory_count')) {
        document.getElementById('inventory_count').textContent = inventoryCount;
    }
    let totalHours = 0;
    document.querySelectorAll('#employee_list li[data-id]').forEach(function(li) {
        totalHours += parseFloat(li.getAttribute('data-hours')) || 0;
    });
    if (document.getElementById('total_hours')) {
        document.getElementById('total_hours').textContent = totalHours.toFixed(1);
    }
}

// Inventory button event
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('add_inventory')) {
        document.getElementById('add_inventory').addEventListener('click', addInventoryToJobcard);
    }
    loadLocalDraft();
    updateSummary();
    document.getElementById('jobcardForm').addEventListener('submit', function(e) { e.preventDefault(); });
});
</script>
@endsection
  



