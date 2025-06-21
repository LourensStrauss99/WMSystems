<!-- filepath: resources/views/jobcard/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Job Card Details</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jobcard Number</label>
                            <input type="text" value="{{ $jobcard->jobcard_number }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Date</label>
                            <input type="date" value="{{ $jobcard->job_date }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <input type="text" value="{{ $jobcard->client->name }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Surname</label>
                            <input type="text" value="{{ $jobcard->client->surname }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telephone</label>
                            <input type="text" value="{{ $jobcard->client->telephone }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" value="{{ $jobcard->client->address }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" value="{{ $jobcard->client->email }}" readonly class="form-control bg-light" />
                        </div>
                    </div>

                    <form method="POST" action="{{ route('jobcard.update', $jobcard->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Assign Employees Section -->
                        <div class="mb-3">
                            <label class="form-label">Assign Employees</label>
                            <div class="input-group mb-2">
                                <select id="employee_select" class="form-control select2">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="add_employee" class="btn btn-primary">Add</button>
                            </div>
                            <ul id="employee_list" class="list-group mb-3"></ul>
                            <input type="hidden" name="employees" id="employees_data">
                        </div>

                        <!-- Inventory Section with Live Stock Checking -->
                        <div class="mb-3">
                            <label class="form-label">Inventory Used</label>
                            <div class="mb-2 position-relative">
                                <input type="text" id="inventory_search" class="form-control" placeholder="Search inventory..." autocomplete="off">
                                <ul id="inventory_suggestions" class="list-group position-absolute w-100" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></ul>
                            </div>
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

                            <!-- Stock Alert Display -->
                            <div id="stock_alert" class="alert" style="display: none;"></div>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Stock After</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory_list"></tbody>
                            </table>
                            <input type="hidden" name="inventory_data" id="inventory_data">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Time Spent</label>
                            <select name="time_spent" class="form-control">
                                @for($i = 0; $i <= 8*4; $i++)
                                    @php $minutes = $i * 15; @endphp
                                    <option value="{{ $minutes }}" 
                                        {{ $jobcard->time_spent == $minutes ? 'selected' : '' }}>
                                        {{ sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Work Done</label>
                            <textarea name="work_done" class="form-control" rows="3">{{ $jobcard->work_done }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="assigned" {{ $jobcard->status == 'assigned' ? 'selected' : '' }}>
                                    Assigned
                                </option>
                                <option value="in progress" {{ $jobcard->status == 'in progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ $jobcard->status == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('jobcard.index') }}" class="btn btn-secondary">
                                ← Back to Search
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Save Jobcard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    // Initial data from backend
    let employeeList = @json($jobcard->employees->map(fn($e) => ['id'=>$e->id, 'name'=>$e->name])->values());
    let inventoryList = @json($jobcard->inventory->map(fn($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'quantity' => (int) $item->pivot->quantity
    ])->values());
    const allEmployees = @json($employees->map(fn($e) => ['id'=>$e->id, 'name'=>$e->name])->values());
    const inventoryOptions = @json($inventory->map(fn($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'short' => $item->short_description,
        'short_code' => $item->short_code,
        'stock_level' => $item->stock_level
    ])->values());

    // --- Employees ---
    function renderEmployeeList() {
        const ul = $('#employee_list');
        ul.empty();
        employeeList.forEach(emp => {
            ul.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${emp.name}
                    <button type="button" class="btn btn-danger btn-sm remove-employee" data-id="${emp.id}">Remove</button>
                </li>
            `);
        });
        $('#employees_data').val(JSON.stringify(employeeList.map(e => e.id)));
    }

    $('#add_employee').click(function() {
        const id = $('#employee_select').val();
        const name = $('#employee_select option:selected').text();
        if (!id || employeeList.find(e => e.id == id)) return;
        employeeList.push({id: parseInt(id), name});
        renderEmployeeList();
        $('#employee_select').val('').trigger('change');
    });

    $(document).on('click', '.remove-employee', function() {
        const id = $(this).data('id');
        employeeList = employeeList.filter(emp => emp.id != id);
        renderEmployeeList();
    });

    // --- Inventory ---
    function renderInventoryList() {
        const tbody = $('#inventory_list');
        tbody.empty();
        inventoryList.forEach(item => {
            // Get current stock info
            const option = $(`#inventory_select option[value="${item.id}"]`);
            const currentStock = parseInt(option.data('stock')) || 0;
            const minLevel = parseInt(option.data('min')) || 0;
            const shortCode = option.data('code') || '';
            const stockAfter = currentStock - item.quantity;
            
            let statusBadge = '';
            if (stockAfter < 0) {
                statusBadge = '<span class="badge bg-danger">❌ Out of Stock</span>';
            } else if (stockAfter <= minLevel) {
                statusBadge = '<span class="badge bg-warning">⚠️ Below Min</span>';
            } else {
                statusBadge = '<span class="badge bg-success">✅ Available</span>';
            }
            
            tbody.append(`
                <tr data-id="${item.id}">
                    <td>
                        <strong>[${shortCode}]</strong><br>
                        ${item.name}
                    </td>
                    <td>${item.quantity}</td>
                    <td>${stockAfter >= 0 ? stockAfter : 'N/A'}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm edit-inventory">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm remove-inventory">Remove</button>
                    </td>
                </tr>
            `);
        });
        $('#inventory_data').val(JSON.stringify(inventoryList));
    }

    $('#add_inventory').click(function() {
        const id = $('#inventory_select').val();
        const name = $('#inventory_select option:selected').text();
        const quantity = parseInt($('#inventory_quantity').val());
        if (!id || !quantity || quantity < 1) return;
        let existingItem = inventoryList.find(item => item.id == id);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            inventoryList.push({id: parseInt(id), name, quantity});
        }
        renderInventoryList();
        $('#inventory_select').val('').trigger('change');
        $('#inventory_quantity').val('1');
        $('#inventory_search').val('');
        $('#inventory_suggestions').hide();
    });

    $(document).on('click', '.remove-inventory', function() {
        const id = $(this).closest('tr').data('id');
        inventoryList = inventoryList.filter(item => item.id != id);
        renderInventoryList();
    });

    $(document).on('click', '.edit-inventory', function() {
        const tr = $(this).closest('tr');
        const id = tr.data('id');
        const item = inventoryList.find(item => item.id == id);
        $('#inventory_select').val(id);
        $('#inventory_quantity').val(item.quantity);
        inventoryList = inventoryList.filter(item => item.id != id);
        renderInventoryList();
    });

    // --- Inventory Search ---
    $('#inventory_search').on('input', function() {
        const search = $(this).val().toLowerCase();
        const suggestions = $('#inventory_suggestions');
        suggestions.empty();
        if (!search) {
            suggestions.hide();
            return;
        }
        const filtered = inventoryOptions.filter(item =>
            item.name.toLowerCase().includes(search) ||
            (item.short || '').toLowerCase().includes(search) ||
            (item.short_code || '').toLowerCase().includes(search)
        );
        filtered.forEach(item => {
            suggestions.append(`
                <li class="list-group-item list-group-item-action" data-id="${item.id}">
                    <strong>[${item.short_code}]</strong> ${item.name} 
                    <small class="text-muted">(Stock: ${item.stock_level})</small>
                </li>
            `);
        });
        suggestions.show();
    });

    $(document).on('click', '#inventory_suggestions li', function() {
        const id = $(this).data('id');
        $('#inventory_select').val(id);
        $('#inventory_search').val($(this).text());
        $('#inventory_suggestions').hide();
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#inventory_search, #inventory_suggestions').length) {
            $('#inventory_suggestions').hide();
        }
    });

    // Update the inventory JavaScript section
    $('#inventory_select, #inventory_quantity').on('change', function() {
        const itemId = $('#inventory_select').val();
        const quantity = parseInt($('#inventory_quantity').val()) || 1;
        
        if (itemId) {
            checkStock(itemId, quantity);
        } else {
            $('#stock_alert').hide();
        }
    });

    function checkStock(itemId, quantity) {
        fetch('/inventory/check-stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            const alertDiv = $('#stock_alert');
            
            if (data.available) {
                alertDiv.removeClass('alert-danger alert-warning')
                       .addClass('alert-success')
                       .html(`
                           <strong>${data.message}</strong><br>
                           Current Stock: ${data.current_stock} | After Use: ${data.remaining_after}
                           ${data.warning ? '<br><span class="text-warning">' + data.warning + '</span>' : ''}
                       `)
                       .show();
                $('#add_inventory').prop('disabled', false);
            } else {
                alertDiv.removeClass('alert-success alert-warning')
                       .addClass('alert-danger')
                       .html(`
                           <strong>${data.message}</strong><br>
                           <small>Please reduce quantity or choose a different item.</small>
                       `)
                       .show();
                $('#add_inventory').prop('disabled', true);
            }
        })
        .catch(error => {
            console.error('Error checking stock:', error);
        });
    }

    // Real-time stock checking when adding inventory
    $('#add_inventory').on('click', function(e) {
        e.preventDefault();
        
        const itemId = $('#inventory_select').val();
        const quantity = parseInt($('#inventory_quantity').val()) || 1;
        
        if (!itemId) {
            alert('Please select an inventory item');
            return;
        }
        
        // Check stock availability first
        checkStockAvailability(itemId, quantity, function(stockData) {
            if (stockData.available) {
                // Stock is available, proceed with adding
                addInventoryToList();
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
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

    // Update the showStockError function to include short code
    function showStockError(stockData) {
        // Get the short code from the selected option
        const selectedOption = $('#inventory_select option:selected');
        const shortCode = selectedOption.data('code') || 'N/A';
        
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>❌ Insufficient Stock!</strong><br>
                <strong>Code:</strong> [${shortCode}]<br>
                <strong>Item:</strong> ${stockData.item_name}<br>
                <strong>Available:</strong> ${stockData.current_stock}<br>
                <strong>Requested:</strong> ${stockData.requested_quantity}<br>
                <strong>Message:</strong> ${stockData.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Show the alert above the inventory section
        $('#inventory_select').closest('.mb-3').prepend(alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $('.alert-danger').fadeOut();
        }, 5000);
    }

    function showStockWarning(stockData) {
        if (stockData.warning) {
            const alertHtml = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>⚠️ Stock Warning!</strong><br>
                    ${stockData.warning}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('#inventory_select').closest('.mb-3').prepend(alertHtml);
            
            setTimeout(function() {
                $('.alert-warning').fadeOut();
            }, 5000);
        }
    }

    // Update the addInventoryToList function to display short code in the table
    function addInventoryToList() {
        const selectElement = document.getElementById('inventory_select');
        const itemId = selectElement.value;
        const quantity = parseInt(document.getElementById('inventory_quantity').value) || 1;
        
        if (!itemId) {
            alert('Please select an inventory item');
            return;
        }
        
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const shortCode = selectedOption.getAttribute('data-code');
        const itemName = selectedOption.text.split(' (Stock:')[0]; // Clean up the display name
        
        // Check if item is already in the list
        const existingItem = inventoryList.find(item => item.id == itemId);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            inventoryList.push({
                id: parseInt(itemId),
                name: itemName,
                short_code: shortCode,
                quantity: quantity
            });
        }
        
        renderInventoryList();
        
        // Reset form
        selectElement.value = '';
        document.getElementById('inventory_quantity').value = 1;
        
        // Remove any existing alerts
        $('.alert').remove();
    }

    // Also add live stock checking when quantity changes
    $('#inventory_quantity').on('input', function() {
        const itemId = $('#inventory_select').val();
        const quantity = parseInt($(this).val()) || 1;
        
        if (itemId && quantity > 0) {
            checkStockAvailability(itemId, quantity, function(stockData) {
                // Remove previous alerts
                $('.alert').remove();
                
                if (!stockData.available) {
                    $('#add_inventory').prop('disabled', true);
                    showStockError(stockData);
                } else {
                    $('#add_inventory').prop('disabled', false);
                    if (stockData.warning) {
                        showStockWarning(stockData);
                    }
                }
            });
        }
    });

    // Check stock when item is selected
    $('#inventory_select').on('change', function() {
        const itemId = $(this).val();
        const quantity = parseInt($('#inventory_quantity').val()) || 1;
        
        // Remove previous alerts
        $('.alert').remove();
        
        if (itemId) {
            checkStockAvailability(itemId, quantity, function(stockData) {
                if (!stockData.available) {
                    $('#add_inventory').prop('disabled', true);
                    showStockError(stockData);
                } else {
                    $('#add_inventory').prop('disabled', false);
                    if (stockData.warning) {
                        showStockWarning(stockData);
                    }
                }
            });
        } else {
            $('#add_inventory').prop('disabled', false);
        }
    });

    // Initial render
    renderEmployeeList();
    renderInventoryList();
});
</script>
@endpush

<meta name="csrf-token" content="{{ csrf_token() }}">