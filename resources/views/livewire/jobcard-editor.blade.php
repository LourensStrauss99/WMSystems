{{-- filepath: resources/views/livewire/jobcard-editor.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-edit text-primary me-2"></i>
                Edit Jobcard
            </h2>
            <p class="text-muted">Update jobcard details and manage resources</p>
        </div>
        <div class="col-md-4 text-end">
             <div class="btn-group" role="group">
                <a href="{{ route('jobcard.pdf', $jobcard->id) }}" class="btn btn-danger text-white" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
                <a href="{{ route('jobcard.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('jobcard.update', $jobcard->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Jobcard Number</label>
                                    <input type="text" name="jobcard_number" class="form-control" 
                                           value="{{ old('jobcard_number', $jobcard->jobcard_number) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Job Date</label>
                                    <input type="date" name="job_date" class="form-control" 
                                           value="{{ old('job_date', $jobcard->job_date) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Client</label>
                                    <select name="client_id" class="form-control" required>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ $jobcard->client_id == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Category</label>
                                    <input type="text" name="category" class="form-control" 
                                           value="{{ old('category', $jobcard->category) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Work Request</label>
                            <textarea name="work_request" class="form-control" rows="3">{{ old('work_request', $jobcard->work_request) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Special Request</label>
                            <textarea name="special_request" class="form-control" rows="3">{{ old('special_request', $jobcard->special_request) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Work Done</label>
                            <textarea name="work_done" class="form-control" rows="4">{{ old('work_done', $jobcard->work_done) }}</textarea>
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
                                @forelse($jobcard->inventory as $item)
                                    <li data-id="{{ $item->id }}" class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <div>
                                            <strong>{{ $item->name }}</strong>
                                            <span class="badge bg-warning text-dark ms-2">Qty: {{ $item->pivot->quantity ?? 0 }}</span>
                                        </div>
                                        <input type="hidden" name="inventory_items[]" value="{{ $item->id }}">
                                        <input type="hidden" name="inventory_qty[{{ $item->id }}]" value="{{ $item->pivot->quantity ?? 0 }}">
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
                
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status Card -->
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
                            <button type="submit" class="btn btn-success text-white">
                                <i class="fas fa-save me-2"></i>Update Jobcard
                            </button>
                            
                            <a href="{{ route('jobcard.pdf', $jobcard->id) }}" 
                               class="btn btn-danger text-white" 
                               target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card shadow-sm">
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
            </div>
        </div>
    </form>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
// Enhanced hour calculation functions
let companyRates = {
    labour_rate: 750.00,
    overtime_multiplier: 1.50,
    weekend_multiplier: 2.00,
    holiday_multiplier: 2.50,
    call_out_rate: 1000.00,
    mileage_rate: 7.50
};

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

    // Prevent duplicate
    if (document.querySelector('#employee_list li[data-id="'+id+'"]')) {
        alert('This employee is already assigned');
        return;
    }

    // Remove "no employees" message
    const noEmployeesMsg = document.querySelector('#employee_list .text-muted');
    if (noEmployeesMsg && noEmployeesMsg.textContent.includes('No employees')) {
        noEmployeesMsg.remove();
    }

    // Calculate cost for this employee
    let rate = companyRates.labour_rate;
    let hourTypeLabel = 'Normal';
    let badgeClass = 'bg-secondary';
    
    switch(hourType) {
        case 'overtime':
            rate = companyRates.labour_rate * companyRates.overtime_multiplier;
            hourTypeLabel = 'Overtime';
            badgeClass = 'bg-warning text-dark';
            break;
        case 'weekend':
            rate = companyRates.labour_rate * companyRates.weekend_multiplier;
            hourTypeLabel = 'Weekend';
            badgeClass = 'bg-primary';
            break;
        case 'holiday':
            rate = companyRates.labour_rate * companyRates.holiday_multiplier;
            hourTypeLabel = 'Holiday';
            badgeClass = 'bg-danger';
            break;
    }
    
    const cost = hours * rate;

    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.setAttribute('data-hours', hours);
    li.setAttribute('data-type', hourType);
    li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
    li.innerHTML = `
        <div>
            <strong>${name}</strong>
            <span class="badge bg-info ms-2">${hours} hrs</span>
            <span class="badge ${badgeClass} ms-1">${hourTypeLabel}</span>
            <small class="text-muted ms-2">R${cost.toFixed(2)}</small>
        </div>
        <input type="hidden" name="employees[]" value="${id}">
        <input type="hidden" name="employee_hours[${id}]" value="${hours}">
        <input type="hidden" name="employee_hour_types[${id}]" value="${hourType}">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee(this)">
            <i class="fas fa-times"></i>
        </button>`;
    document.getElementById('employee_list').appendChild(li);
    
    // Reset form
    select.value = '';
    document.getElementById('employee_hours_input').value = '';
    document.getElementById('hour_type_select').value = 'normal';
    
    updateHourTotals();
    updateSummary();
}

function removeEmployee(btn) {
    btn.closest('li').remove();
    
    // Add "no employees" message if list is empty
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
    
    // Calculate from employee assignments
    document.querySelectorAll('#employee_list li[data-id]').forEach(function(li) {
        const hours = parseFloat(li.getAttribute('data-hours')) || 0;
        const type = li.getAttribute('data-type') || 'normal';
        let rate = companyRates.labour_rate;
        
        switch(type) {
            case 'normal':
                normalHours += hours;
                rate = companyRates.labour_rate;
                break;
            case 'overtime':
                overtimeHours += hours;
                rate = companyRates.labour_rate * companyRates.overtime_multiplier;
                break;
            case 'weekend':
                weekendHours += hours;
                rate = companyRates.labour_rate * companyRates.weekend_multiplier;
                break;
            case 'holiday':
                holidayHours += hours;
                rate = companyRates.labour_rate * companyRates.holiday_multiplier;
                break;
        }
        
        totalLabourCost += hours * rate;
    });
    
    // Add call out fee and mileage
    const callOutFee = parseFloat(document.getElementById('call_out_fee').value) || 0;
    const mileageKm = parseFloat(document.getElementById('mileage_km').value) || 0;
    const mileageCost = mileageKm * companyRates.mileage_rate;
    
    totalLabourCost += callOutFee + mileageCost;
    
    // Update hidden fields (ONLY these ones should exist)
    document.getElementById('hidden_normal_hours').value = normalHours.toFixed(2);
    document.getElementById('hidden_overtime_hours').value = overtimeHours.toFixed(2);
    document.getElementById('hidden_weekend_hours').value = weekendHours.toFixed(2);
    document.getElementById('hidden_public_holiday_hours').value = holidayHours.toFixed(2);
    document.getElementById('hidden_total_labour_cost').value = totalLabourCost.toFixed(2);
    
    // Update mileage cost field
    document.getElementById('mileage_cost').value = mileageCost.toFixed(2);
    
    // Update display
    document.getElementById('display_total_labour_cost').textContent = totalLabourCost.toFixed(2);
}

// Add this function to your JavaScript section
function recalculateFromExisting() {
    // Recalculate totals from existing employee data on page load
    let normalHours = 0, overtimeHours = 0, weekendHours = 0, holidayHours = 0;
    let totalLabourCost = 0;
    
    document.querySelectorAll('#employee_list li[data-id]').forEach(function(li) {
        const hours = parseFloat(li.getAttribute('data-hours')) || 0;
        const type = li.getAttribute('data-type') || 'normal';
        let rate = companyRates.labour_rate;
        
        switch(type) {
            case 'normal':
                normalHours += hours;
                rate = companyRates.labour_rate;
                break;
            case 'overtime':
                overtimeHours += hours;
                rate = companyRates.labour_rate * companyRates.overtime_multiplier;
                break;
            case 'weekend':
                weekendHours += hours;
                rate = companyRates.labour_rate * companyRates.weekend_multiplier;
                break;
            case 'holiday':
                holidayHours += hours;
                rate = companyRates.labour_rate * companyRates.holiday_multiplier;
                break;
        }
        
        totalLabourCost += hours * rate;
    });
    
    // Add call out fee and mileage from existing values
    const callOutFee = parseFloat(document.getElementById('call_out_fee').value) || 0;
    const mileageKm = parseFloat(document.getElementById('mileage_km').value) || 0;
    const mileageCost = mileageKm * companyRates.mileage_rate;
    
    totalLabourCost += callOutFee + mileageCost;
    
    // Update hidden fields
    document.getElementById('hidden_normal_hours').value = normalHours.toFixed(2);
    document.getElementById('hidden_overtime_hours').value = overtimeHours.toFixed(2);
    document.getElementById('hidden_weekend_hours').value = weekendHours.toFixed(2);
    document.getElementById('hidden_public_holiday_hours').value = holidayHours.toFixed(2);
    document.getElementById('hidden_total_labour_cost').value = totalLabourCost.toFixed(2);
    
    // Update mileage cost field
    document.getElementById('mileage_cost').value = mileageCost.toFixed(2);
    
    // Update display
    document.getElementById('display_total_labour_cost').textContent = totalLabourCost.toFixed(2);
}

function calculateTotalCosts() {
    updateHourTotals();
}

function setStandardCallOut() {
    document.getElementById('call_out_fee').value = companyRates.call_out_rate;
    updateHourTotals();
}

function updateSummary() {
    // Update employee count
    const employeeCount = document.querySelectorAll('#employee_list li[data-id]').length;
    if (document.getElementById('employee_count')) {
        document.getElementById('employee_count').textContent = employeeCount;
    }
    
    // Update inventory count  
    const inventoryCount = document.querySelectorAll('#inventory_list li[data-id]').length;
    if (document.getElementById('inventory_count')) {
        document.getElementById('inventory_count').textContent = inventoryCount;
    }
    
    // Update total hours from employee assignments
    let totalHours = 0;
    document.querySelectorAll('#employee_list li[data-id]').forEach(function(li) {
        totalHours += parseFloat(li.getAttribute('data-hours')) || 0;
    });
    
    if (document.getElementById('total_hours')) {
        document.getElementById('total_hours').textContent = totalHours.toFixed(1);
    }
}

// Inventory functions
function addInventoryToJobcard() {
    let select = document.getElementById('inventory_select');
    let qty = document.getElementById('inventory_quantity').value;
    let id = select.value;
    let name = select.options[select.selectedIndex].text;

    if (!id || !qty || qty < 1) {
        alert('Please select an item and enter valid quantity');
        return;
    }

    // Prevent duplicate
    if (document.querySelector('#inventory_list li[data-id="'+id+'"]')) {
        alert('This item is already added to the jobcard');
        return;
    }

    // Remove "no items" message
    const noItemsMsg = document.querySelector('#inventory_list .text-muted');
    if (noItemsMsg && noItemsMsg.textContent.includes('No inventory')) {
        noItemsMsg.remove();
    }

    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
    li.innerHTML = `
        <div>
            <strong>${name.split(' (Stock:')[0]}</strong>
            <span class="badge bg-warning text-dark ms-2">Qty: ${qty}</span>
        </div>
        <input type="hidden" name="inventory_items[]" value="${id}">
        <input type="hidden" name="inventory_qty[${id}]" value="${qty}">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInventory(this)">
            <i class="fas fa-times"></i>
        </button>`;
    document.getElementById('inventory_list').appendChild(li);
    
    // Reset form
    select.value = '';
    document.getElementById('inventory_quantity').value = 1;
    
    updateSummary();
}

function removeInventory(btn) {
    btn.closest('li').remove();
    
    // Add "no items" message if list is empty
    const list = document.getElementById('inventory_list');
    if (list.children.length === 0) {
        list.innerHTML = '<li class="text-muted">No inventory items added yet</li>';
    }
    
    updateSummary();
}

// Load company rates and initialize
document.addEventListener('DOMContentLoaded', function() {
    // Fetch actual rates from your company settings
    fetch('/api/company-rates')
        .then(response => response.json())
        .then(data => {
            companyRates = data;
            updateRateDisplays();
            recalculateFromExisting(); // Use this instead of updateHourTotals
        })
        .catch(error => {
            console.log('Using default rates');
            updateRateDisplays();
            recalculateFromExisting(); // Use this instead of updateHourTotals
        });
    
    // Add inventory button event listener
    if (document.getElementById('add_inventory')) {
        document.getElementById('add_inventory').addEventListener('click', addInventoryToJobcard);
    }
    
    updateSummary();
});

function updateRateDisplays() {
    // Update rate displays in the hour type select
    const normalRateSpan = document.querySelector('.normal-rate');
    const overtimeRateSpan = document.querySelector('.overtime-rate');
    const weekendRateSpan = document.querySelector('.weekend-rate');
    const holidayRateSpan = document.querySelector('.holiday-rate');
    const mileageRateSpan = document.getElementById('mileage_rate');
    
    if (normalRateSpan) normalRateSpan.textContent = companyRates.labour_rate.toFixed(0);
    if (overtimeRateSpan) overtimeRateSpan.textContent = (companyRates.labour_rate * companyRates.overtime_multiplier).toFixed(0);
    if (weekendRateSpan) weekendRateSpan.textContent = (companyRates.labour_rate * companyRates.weekend_multiplier).toFixed(0);
    if (holidayRateSpan) holidayRateSpan.textContent = (companyRates.labour_rate * companyRates.holiday_multiplier).toFixed(0);
    if (mileageRateSpan) mileageRateSpan.textContent = companyRates.mileage_rate.toFixed(2);
    
    // Update option labels
    const hourTypeSelect = document.getElementById('hour_type_select');
    if (hourTypeSelect) {
        hourTypeSelect.innerHTML = `
            <option value="normal">Normal (R${companyRates.labour_rate.toFixed(0)}/hr)</option>
            <option value="overtime">Overtime (R${(companyRates.labour_rate * companyRates.overtime_multiplier).toFixed(0)}/hr)</option>
            <option value="weekend">Weekend (R${(companyRates.labour_rate * companyRates.weekend_multiplier).toFixed(0)}/hr)</option>
            <option value="holiday">Holiday (R${(companyRates.labour_rate * companyRates.holiday_multiplier).toFixed(0)}/hr)</option>
        `;
    }
}
</script>
@endsection



