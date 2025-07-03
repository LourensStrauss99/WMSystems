{{-- filepath: resources/views/purchase-orders/edit.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold">
                <i class="fas fa-edit text-warning me-2"></i>
                Edit Purchase Order: {{ $purchaseOrder->po_number }}
            </h2>
            <p class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Modify purchase order details • Status: 
                <span class="badge bg-{{ $purchaseOrder->status === 'draft' ? 'secondary' : 'danger' }}">
                    {{ ucfirst($purchaseOrder->status) }}
                </span>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to View
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:
            </h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Purchase Order Edit Form -->
    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" id="editPOForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Left Column - PO Details -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt me-2"></i>Purchase Order Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supplier_id" class="form-label fw-bold">
                                    <i class="fas fa-building me-1"></i>Supplier *
                                </label>
                                <select name="supplier_id" id="supplier_id" 
                                        class="form-select @error('supplier_id') is-invalid @enderror" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                                {{ $purchaseOrder->supplier_id == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="order_date" class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Order Date *
                                </label>
                                <input type="date" name="order_date" id="order_date" 
                                       class="form-control @error('order_date') is-invalid @enderror" 
                                       value="{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : date('Y-m-d') }}" required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-hashtag me-1"></i>PO Number
                                </label>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ $purchaseOrder->po_number }}" readonly>
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i>PO number cannot be changed
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order Items -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Order Items
                            </h5>
                            <!-- Remove the btn-group, keep only inventory button -->
                            <button type="button" class="btn btn-light btn-sm" onclick="toggleInventoryPanel()">
                                <i class="fas fa-box me-1"></i>Add from Inventory
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Add from Inventory Panel -->
                        <div id="inventory-panel" class="border rounded p-3 mb-3 bg-light" style="display: none;">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-box me-1"></i>Add Item from Inventory
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Select Inventory Item</label>
                                    <!-- Add the missing onchange handler -->
                                    <select id="inventory-select" class="form-select" onchange="populateInventoryFields()">
                                        <option value="">Choose an item...</option>
                                        @foreach($inventory as $item)
                                            <option value="{{ $item->id }}" 
                                                    data-name="{{ $item->name }}" 
                                                    data-code="{{ $item->code ?? $item->short_code }}" 
                                                    data-description="{{ $item->description ?? '' }}"
                                                    data-category="{{ $item->category ?? '' }}"
                                                    data-price="{{ $item->buying_price ?? 0 }}">
                                                {{ $item->name }} {{ ($item->code ?? $item->short_code) ? '(' . ($item->code ?? $item->short_code) . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Quantity *</label>
                                    <input type="number" id="inventory-quantity" min="0.001" step="0.001" class="form-control" placeholder="0">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Unit Price (R) *</label>
                                    <input type="number" id="inventory-price" step="0.01" min="0" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            
                            <!-- Show selected item details -->
                            <div id="selected-item-details" class="row mt-2" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <strong>Selected Item:</strong> <span id="selected-name"></span><br>
                                        <strong>Code:</strong> <span id="selected-code"></span><br>
                                        <strong>Description:</strong> <span id="selected-description"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-success" onclick="addInventoryItem()">
                                    <i class="fas fa-plus me-1"></i>Add to Order
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleInventoryPanel()">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Items List -->
                        <div id="items-list">
                            <!-- Pre-populate existing items -->
                            @foreach($purchaseOrder->items as $item)
                                <div class="item-display border rounded p-3 mb-3 bg-white" data-item-index="{{ $loop->index }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-box me-1"></i>{{ $item->item_name }}
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem({{ $loop->index }})" title="Remove Item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="row small text-muted">
                                        <div class="col-md-6">
                                            @if($item->item_code)
                                                <div><strong>Code:</strong> {{ $item->item_code }}</div>
                                            @endif
                                            @if($item->item_description)
                                                <div><strong>Description:</strong> {{ $item->item_description }}</div>
                                            @endif
                                            @if($item->inventory_id)
                                                <div class="text-success"><strong>From Inventory</strong></div>
                                            @else
                                                <div class="text-warning"><strong>New Item</strong></div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <div><strong>Quantity:</strong> {{ $item->quantity_ordered }}</div>
                                            <div><strong>Unit Price:</strong> R {{ number_format($item->unit_price, 2) }}</div>
                                            <div class="text-success"><strong>Line Total: R {{ number_format($item->line_total, 2) }}</strong></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden form inputs --> 
                                    <input type="hidden" name="items[{{ $loop->index }}][inventory_id]" value="{{ $item->inventory_id }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][item_name]" value="{{ $item->item_name }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][item_code]" value="{{ $item->item_code }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][item_description]" value="{{ $item->item_description }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][quantity_ordered]" value="{{ $item->quantity_ordered }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][unit_price]" value="{{ $item->unit_price }}">
                                </div>
                            @endforeach
                        </div>
                        
                        @if($purchaseOrder->items->count() == 0)
                            <div class="alert alert-info" id="no-items-alert">
                                <i class="fas fa-info-circle me-2"></i>
                                No items in this purchase order. Click "Add from Inventory" to add products.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calculator me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-row d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">R {{ number_format($purchaseOrder->calculateSubtotal(), 2) }}</span>
                        </div>
                        <div class="summary-row d-flex justify-content-between mb-2">
                            <span>VAT (15%):</span>
                            <span id="vat-amount">R {{ number_format($purchaseOrder->vat_amount ?? 0, 2) }}</span>
                        </div>
                        <hr>
                        <div class="summary-row d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="grand-total">R {{ number_format($purchaseOrder->grand_total ?? 0, 2) }}</span>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="total-items">{{ $purchaseOrder->items->count() }}</span> item(s) in this order
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Amendment History -->
                @if($purchaseOrder->amended_at)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>Amendment History
                            </h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <div><strong>Last Amended:</strong> {{ $purchaseOrder->amended_at->format('d M Y H:i') }}</div>
                                <div><strong>Amended By:</strong> {{ $purchaseOrder->amendedBy->name ?? 'Unknown' }}</div>
                            </small>
                        </div>
                    </div>
                @endif

                <!-- Rejection History - New Section -->
                @if($purchaseOrder->status === 'rejected' || $purchaseOrder->getRejectionCount() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Rejection History
                                @if($purchaseOrder->getRejectionCount() > 1)
                                    <span class="badge bg-danger">{{ $purchaseOrder->getRejectionCount() }} rejections</span>
                                @endif
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($purchaseOrder->status === 'rejected')
                                <div class="alert alert-danger">
                                    <strong>Current Status:</strong> Rejected<br>
                                    <strong>Latest Reason:</strong> {{ $purchaseOrder->getLatestRejectionReason() ?: 'No reason provided' }}
                                </div>
                            @endif
                            
                            @if($purchaseOrder->getRejectionHistory())
                                <h6>Previous Rejections:</h6>
                                @foreach($purchaseOrder->getRejectionHistory() as $index => $rejection)
                                    <div class="border-start border-danger ps-3 mb-2">
                                        <small class="text-muted">
                                            <strong>Version {{ $rejection['po_version'] ?? ($index + 1) }}:</strong> 
                                            {{ isset($rejection['rejected_at']) ? date('d M Y H:i', strtotime($rejection['rejected_at'])) : 'Date unknown' }}
                                        </small>
                                        <p class="mb-0">{{ $rejection['reason'] }}</p>
                                    </div>
                                @endforeach
                            @endif
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                After updating this order, it will be reset to draft status and can be resubmitted for approval.
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>Update Purchase Order
                    </button>
                    <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let orderItems = [];

// Initialize with existing items
@foreach($purchaseOrder->items as $index => $item)
    orderItems.push({
        index: {{ $index }},
        inventoryId: {{ $item->inventory_id ?? 'null' }},
        name: "{{ addslashes($item->item_name) }}",
        code: "{{ addslashes($item->item_code ?? '') }}",
        description: "{{ addslashes($item->item_description ?? '') }}",
        category: "{{ addslashes($item->item_category ?? '') }}",
        quantity: {{ $item->quantity_ordered }},
        price: {{ $item->unit_price }},
        total: {{ $item->line_total }}
    });
@endforeach

let nextItemIndex = {{ $purchaseOrder->items->count() }};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit page loaded with', orderItems.length, 'existing items');
    calculateOrderTotal();
});

function toggleInventoryPanel() {
    const panel = document.getElementById('inventory-panel');
    
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        document.getElementById('inventory-select').focus();
    } else {
        panel.style.display = 'none';
        clearInventoryForm();
    }
}

function populateInventoryFields() {
    const select = document.getElementById('inventory-select');
    const selectedOption = select.options[select.selectedIndex];
    const detailsDiv = document.getElementById('selected-item-details');
    
    if (select.value) {
        // Populate price field from buying_price
        document.getElementById('inventory-price').value = selectedOption.dataset.price || '';
        
        // Show item details
        document.getElementById('selected-name').textContent = selectedOption.dataset.name || '';
        document.getElementById('selected-code').textContent = selectedOption.dataset.code || 'N/A';
        document.getElementById('selected-description').textContent = selectedOption.dataset.description || 'N/A';
        
        detailsDiv.style.display = 'block';
        
        // Focus on quantity field
        document.getElementById('inventory-quantity').focus();
    } else {
        // Clear fields and hide details
        document.getElementById('inventory-price').value = '';
        detailsDiv.style.display = 'none';
    }
}

function clearInventoryForm() {
    document.getElementById('inventory-select').value = '';
    document.getElementById('inventory-quantity').value = '';
    document.getElementById('inventory-price').value = '';
    document.getElementById('selected-item-details').style.display = 'none';
}

function addInventoryItem() {
    const select = document.getElementById('inventory-select');
    const selectedOption = select.options[select.selectedIndex];
    const quantity = parseFloat(document.getElementById('inventory-quantity').value);
    const price = parseFloat(document.getElementById('inventory-price').value);
    
    if (!select.value || !quantity || quantity <= 0 || price < 0) {
        alert('Please select an item and enter valid quantity and price.');
        return;
    }
    
    const item = {
        index: nextItemIndex++,
        inventoryId: select.value,
        name: selectedOption.dataset.name,
        code: selectedOption.dataset.code || '',
        description: selectedOption.dataset.description || '',
        category: selectedOption.dataset.category || '',
        quantity: quantity,
        price: price,
        total: quantity * price
    };
    
    orderItems.push(item);
    renderItemsList();
    calculateOrderTotal();
    toggleInventoryPanel();
    clearInventoryForm();
}

function renderItemsList() {
    const itemsList = document.getElementById('items-list');
    const noItemsAlert = document.getElementById('no-items-alert');
    
    if (orderItems.length === 0) {
        itemsList.innerHTML = '';
        if (noItemsAlert) noItemsAlert.style.display = 'block';
        return;
    }
    
    if (noItemsAlert) noItemsAlert.style.display = 'none';
    
    itemsList.innerHTML = orderItems.map((item, index) => `
        <div class="item-display border rounded p-3 mb-3 bg-white" data-item-index="${item.index}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 text-primary">
                    <i class="fas fa-box me-1"></i>${item.name}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.index})" title="Remove Item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="row small text-muted">
                <div class="col-md-6">
                    ${item.code ? `<div><strong>Code:</strong> ${item.code}</div>` : ''}
                    ${item.description ? `<div><strong>Description:</strong> ${item.description}</div>` : ''}
                    <div class="text-success"><strong>From Inventory</strong></div>
                </div>
                <div class="col-md-6">
                    <div><strong>Quantity:</strong> ${item.quantity}</div>
                    <div><strong>Unit Price:</strong> R ${item.price.toFixed(2)}</div>
                    <div class="text-success"><strong>Line Total: R ${item.total.toFixed(2)}</strong></div>
                </div>
            </div>
            
            <!-- Hidden form inputs --> 
            <input type="hidden" name="items[${index}][inventory_id]" value="${item.inventoryId || ''}">
            <input type="hidden" name="items[${index}][item_name]" value="${item.name}">
            <input type="hidden" name="items[${index}][item_code]" value="${item.code || ''}">
            <input type="hidden" name="items[${index}][item_description]" value="${item.description || ''}">
            <input type="hidden" name="items[${index}][quantity_ordered]" value="${item.quantity}">
            <input type="hidden" name="items[${index}][unit_price]" value="${item.price}">
        </div>
    `).join('');
}

function removeItem(itemIndex) {
    if (confirm('Are you sure you want to remove this item?')) {
        orderItems = orderItems.filter(item => item.index !== itemIndex);
        renderItemsList();
        calculateOrderTotal();
    }
}

function calculateOrderTotal() {
    let subtotal = 0;
    
    orderItems.forEach(item => {
        subtotal += item.total;
    });
    
    const vatAmount = subtotal * 0.15;
    const grandTotal = subtotal + vatAmount;
    
    document.getElementById('subtotal').textContent = 'R ' + subtotal.toFixed(2);
    document.getElementById('vat-amount').textContent = 'R ' + vatAmount.toFixed(2);
    document.getElementById('grand-total').textContent = 'R ' + grandTotal.toFixed(2);
    document.getElementById('total-items').textContent = orderItems.length;
}
</script>

<style>
.item-display {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.item-display:hover {
    background-color: #e9ecef;
}

.summary-row {
    font-size: 1.1rem;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

#inventory-panel {
    border: 2px dashed #28a745;
    background-color: #d4edda !important;
}

#new-item-form {
    border: 2px dashed #007bff;
    background-color: #cce7ff !important;
}
</style>
@endsection