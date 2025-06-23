{{-- filepath: resources/views/livewire/jobcard-editor.blade.php --}}
@extends('layouts.app')

@section('content')
<!-- Add custom CSS for button styling -->
<style>
.btn-save-custom {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: white !important;
    transition: all 0.3s ease !important;
}

.btn-save-custom:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
}

.btn-pdf-custom {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
    transition: all 0.3s ease !important;
}

.btn-pdf-custom:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
}

.btn-back-custom {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: white !important;
    transition: all 0.3s ease !important;
}

.btn-back-custom:hover {
    background-color: #5a6268 !important;
    border-color: #545b62 !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
}

.btn i {
    margin-right: 8px;
}
</style>

<div class="container mt-4">
    <h2>Edit Jobcard</h2>
    <form method="POST" action="{{ route('jobcard.update', $jobcard->id) }}">
        @csrf
        @method('PUT')

        <!-- Basic Jobcard Fields -->
        <div class="mb-3">
            <label class="form-label">Jobcard Number</label>
            <input type="text" name="jobcard_number" class="form-control" value="{{ old('jobcard_number', $jobcard->jobcard_number) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Job Date</label>
            <input type="date" name="job_date" class="form-control" value="{{ old('job_date', $jobcard->job_date) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $jobcard->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="{{ old('category', $jobcard->category) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Work Request</label>
            <textarea name="work_request" class="form-control">{{ old('work_request', $jobcard->work_request) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Special Request</label>
            <textarea name="special_request" class="form-control">{{ old('special_request', $jobcard->special_request) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="assigned" {{ $jobcard->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in progress" {{ $jobcard->status == 'in progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $jobcard->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="invoiced" {{ $jobcard->status == 'invoiced' ? 'selected' : '' }}>Invoiced</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Work Done</label>
            <textarea name="work_done" class="form-control">{{ old('work_done', $jobcard->work_done) }}</textarea>
        </div>

        <!-- Employees Section -->
        <div class="mb-3">
            <label class="form-label">Add Employee</label>
            <div class="row g-2">
                <div class="col-md-5">
                    <select id="employee_select" class="form-control">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" id="employee_hours_input" class="form-control" min="0" placeholder="Hours Worked">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" onclick="addEmployee()">Add</button>
                </div>
            </div>
            <ul id="employee_list" class="mt-2">
                @foreach($jobcard->employees as $employee)
                    <li data-id="{{ $employee->id }}">
                        {{ $employee->name }} ({{ $employee->pivot->hours_worked ?? 0 }} hours)
                        <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                        <input type="hidden" name="employee_hours[{{ $employee->id }}]" value="{{ $employee->pivot->hours_worked ?? 0 }}">
                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeEmployee(this)">Remove</button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Inventory Section -->
        <div class="mb-3">
            <label class="form-label">Add Inventory Item</label>
            <div class="input-group mb-3">
                <select id="inventory_select" class="form-control">
                    <option value="">Select Inventory</option>
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
                <input type="number" id="inventory_quantity" class="form-control" min="1" max="100" value="1" placeholder="Qty">
                <button type="button" id="add_inventory" class="btn btn-primary">Add</button>
            </div>
            <ul id="inventory_list" class="mt-2">
                @foreach($jobcard->inventory as $item)
                    <li data-id="{{ $item->id }}">
                        {{ $item->name }} (Qty: {{ $item->pivot->quantity ?? 0 }})
                        <input type="hidden" name="inventory_items[]" value="{{ $item->id }}">
                        <input type="hidden" name="inventory_qty[{{ $item->id }}]" value="{{ $item->pivot->quantity ?? 0 }}">
                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeInventory(this)">Remove</button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Custom styled buttons -->
        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-save-custom">
                <i class="fas fa-save"></i>Save Jobcard
            </button>
            
            <a href="{{ route('jobcard.pdf', $jobcard->id) }}" 
               class="btn btn-pdf-custom" 
               target="_blank">
                <i class="fas fa-file-pdf"></i>Export PDF
            </a>
            
            <a href="{{ route('jobcard.index') }}" 
               class="btn btn-back-custom">
                <i class="fas fa-arrow-left"></i>Back to List
            </a>
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

    if (!id || !hours || hours < 0) return;

    // Prevent duplicate
    if (document.querySelector('#employee_list li[data-id="'+id+'"]')) return;

    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.innerHTML = `${name} (${hours} hours)
        <input type="hidden" name="employees[]" value="${id}">
        <input type="hidden" name="employee_hours[${id}]" value="${hours}">
        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeEmployee(this)">Remove</button>`;
    document.getElementById('employee_list').appendChild(li);
}

function removeEmployee(btn) {
    btn.parentElement.remove();
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

function addInventoryToJobcard() {
    let select = document.getElementById('inventory_select');
    let qty = document.getElementById('inventory_quantity').value;
    let id = select.value;
    let name = select.options[select.selectedIndex].text;

    if (!id || !qty || qty < 1) return;

    // Prevent duplicate
    if (document.querySelector('#inventory_list li[data-id="'+id+'"]')) {
        alert('This item is already added to the jobcard');
        return;
    }

    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.innerHTML = `${name} (Qty: ${qty})
        <input type="hidden" name="inventory_items[]" value="${id}">
        <input type="hidden" name="inventory_qty[${id}]" value="${qty}">
        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeInventory(this)">Remove</button>`;
    document.getElementById('inventory_list').appendChild(li);
    
    // Reset form
    select.value = '';
    document.getElementById('inventory_quantity').value = 1;
    
    // Remove any existing alerts
    removeAlerts();
}

function removeInventory(btn) {
    btn.parentElement.remove();
}

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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Insufficient Stock!</strong><br>
            <strong>Code:</strong> [${shortCode}]<br>
            <strong>Item:</strong> ${stockData.item_name}<br>
            <strong>Available:</strong> ${stockData.current_stock}<br>
            <strong>Requested:</strong> ${stockData.requested_quantity}<br>
            <strong>Message:</strong> ${stockData.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="removeAlerts()"></button>
        </div>
    `;
    
    // Show the alert above the inventory section
    const inventorySection = document.getElementById('inventory_select').closest('.mb-3');
    inventorySection.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
        removeAlerts();
    }, 5000);
}

function showStockWarning(stockData) {
    if (stockData.warning) {
        const alertHtml = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>⚠️ Stock Warning!</strong><br>
                ${stockData.warning}
                <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="removeAlerts()"></button>
            </div>
        `;
        
        const inventorySection = document.getElementById('inventory_select').closest('.mb-3');
        inventorySection.insertAdjacentHTML('afterbegin', alertHtml);
        
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
</script>
@endsection



