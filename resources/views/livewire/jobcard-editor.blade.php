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

                <!-- Employees Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users text-success me-2"></i>Assigned Employees
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-muted">Employee</label>
                                <select id="employee_select" class="form-control">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted">Hours Worked</label>
                                <input type="number" id="employee_hours_input" class="form-control" 
                                       min="0" step="0.5" placeholder="Hours">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted">&nbsp;</label>
                                <button type="button" class="btn btn-success text-white d-block" onclick="addEmployee()">
                                    <i class="fas fa-plus me-2"></i>Add Employee
                                </button>
                            </div>
                        </div>

                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-list me-2"></i>Current Employees
                            </h6>
                            <ul id="employee_list" class="list-unstyled mb-0">
                                @forelse($jobcard->employees as $employee)
                                    <li data-id="{{ $employee->id }}" class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <div>
                                            <strong>{{ $employee->name }}</strong>
                                            <span class="badge bg-info ms-2">{{ $employee->pivot->hours_worked ?? 0 }} hours</span>
                                        </div>
                                        <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                                        <input type="hidden" name="employee_hours[{{ $employee->id }}]" value="{{ $employee->pivot->hours_worked ?? 0 }}">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </li>
                                @empty
                                    <li class="text-muted">No employees assigned yet</li>
                                @endforelse
                            </ul>
                        </div>
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
function addEmployee() {
    let select = document.getElementById('employee_select');
    let hours = document.getElementById('employee_hours_input').value;
    let id = select.value;
    let name = select.options[select.selectedIndex].text;

    if (!id || !hours || hours < 0) {
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

    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
    li.innerHTML = `
        <div>
            <strong>${name}</strong>
            <span class="badge bg-info ms-2">${hours} hours</span>
        </div>
        <input type="hidden" name="employees[]" value="${id}">
        <input type="hidden" name="employee_hours[${id}]" value="${hours}">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee(this)">
            <i class="fas fa-times"></i>
        </button>`;
    document.getElementById('employee_list').appendChild(li);
    
    // Reset form
    select.value = '';
    document.getElementById('employee_hours_input').value = '';
    
    updateSummary();
}

function removeEmployee(btn) {
    btn.closest('li').remove();
    
    // Add "no employees" message if list is empty
    const list = document.getElementById('employee_list');
    if (list.children.length === 0) {
        list.innerHTML = '<li class="text-muted">No employees assigned yet</li>';
    }
    
    updateSummary();
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
    
    // Remove any existing alerts
    removeAlerts();
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

function updateSummary() {
    // Update employee count
    const employeeCount = document.querySelectorAll('#employee_list li[data-id]').length;
    document.getElementById('employee_count').textContent = employeeCount;
    
    // Update inventory count
    const inventoryCount = document.querySelectorAll('#inventory_list li[data-id]').length;
    document.getElementById('inventory_count').textContent = inventoryCount;
    
    // Update total hours
    let totalHours = 0;
    document.querySelectorAll('#employee_list input[name^="employee_hours"]').forEach(input => {
        totalHours += parseFloat(input.value) || 0;
    });
    document.getElementById('total_hours').textContent = totalHours;
}

// Real-time stock checking when adding inventory
document.getElementById('add_inventory').addEventListener('click', function(e) {
    e.preventDefault();
    
    const itemId = document.getElementById('inventory_select').value;
    const quantity = parseInt(document.getElementById('inventory_quantity').value) || 1;
    
    if (!itemId) {
        alert('Please select an inventory item');
        return;
    }
    
    // Check stock availability first
    checkStockAvailability(itemId, quantity, function(stockData) {
        if (stockData.available) {
            // Stock is available, proceed with adding
            addInventoryToJobcard();
        } else {
            // Stock not available, show error
            showStockError(stockData);
        }
    });
});

function checkStockAvailability(itemId, quantity, callback) {
    fetch('/inventory/check-stock', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        callback(data);
    })
    .catch(error => {
        console.error('Error checking stock:', error);
        alert('Error checking stock availability. Please try again.');
    });
}

function showStockError(stockData) {
    // Get the short code from the selected option
    const selectedOption = document.querySelector('#inventory_select option:checked');
    const shortCode = selectedOption ? selectedOption.getAttribute('data-code') : 'N/A';
    
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <strong><i class="fas fa-exclamation-triangle me-2"></i>Insufficient Stock!</strong><br>
            <strong>Code:</strong> [${shortCode}]<br>
            <strong>Item:</strong> ${stockData.item_name}<br>
            <strong>Available:</strong> ${stockData.current_stock}<br>
            <strong>Requested:</strong> ${stockData.requested_quantity}<br>
            <strong>Message:</strong> ${stockData.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="removeAlerts()"></button>
        </div>
    `;
    
    // Show the alert in the inventory card body
    const inventoryCard = document.querySelector('#inventory_select').closest('.card-body');
    inventoryCard.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
        removeAlerts();
    }, 5000);
}

function showStockWarning(stockData) {
    if (stockData.warning) {
        const alertHtml = `
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>Stock Warning!</strong><br>
                ${stockData.warning}
                <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="removeAlerts()"></button>
            </div>
        `;
        
        const inventoryCard = document.querySelector('#inventory_select').closest('.card-body');
        inventoryCard.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(function() {
            removeAlerts();
        }, 5000);
    }
}

function removeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.display = 'none';
        alert.remove();
    });
}

// Check stock when quantity changes
document.getElementById('inventory_quantity').addEventListener('input', function() {
    const itemId = document.getElementById('inventory_select').value;
    const quantity = parseInt(this.value) || 1;
    
    if (itemId && quantity > 0) {
        checkStockAvailability(itemId, quantity, function(stockData) {
            // Remove previous alerts
            removeAlerts();
            
            if (!stockData.available) {
                document.getElementById('add_inventory').disabled = true;
                showStockError(stockData);
            } else {
                document.getElementById('add_inventory').disabled = false;
                if (stockData.warning) {
                    showStockWarning(stockData);
                }
            }
        });
    }
});

// Check stock when item is selected
document.getElementById('inventory_select').addEventListener('change', function() {
    const itemId = this.value;
    const quantity = parseInt(document.getElementById('inventory_quantity').value) || 1;
    
    // Remove previous alerts
    removeAlerts();
    
    if (itemId) {
        checkStockAvailability(itemId, quantity, function(stockData) {
            if (!stockData.available) {
                document.getElementById('add_inventory').disabled = true;
                showStockError(stockData);
            } else {
                document.getElementById('add_inventory').disabled = false;
                if (stockData.warning) {
                    showStockWarning(stockData);
                }
            }
        });
    } else {
        document.getElementById('add_inventory').disabled = false;
    }
});

// Initialize summary on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});
</script>
@endsection



