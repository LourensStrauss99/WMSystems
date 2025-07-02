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
            <p class="text-muted">Modify purchase order details and items</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to View
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Purchase Order Edit Form -->
    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}" id="editPOForm">
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
                                       value="{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : '' }}" required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expected_delivery_date" class="form-label fw-bold">
                                    <i class="fas fa-truck me-1"></i>Expected Delivery Date
                                </label>
                                <input type="date" name="expected_delivery_date" id="expected_delivery_date" 
                                       class="form-control @error('expected_delivery_date') is-invalid @enderror" 
                                       value="{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : '' }}">
                                @error('expected_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-hashtag me-1"></i>PO Number
                                </label>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ $purchaseOrder->po_number }}" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="notes" class="form-label fw-bold">
                                    <i class="fas fa-sticky-note me-1"></i>Order Notes
                                </label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="form-control @error('notes') is-invalid @enderror" 
                                          placeholder="Additional instructions or notes for this purchase order">{{ $purchaseOrder->notes }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                            <button type="button" class="btn btn-light btn-sm" onclick="showAddItemForm()">
                                <i class="fas fa-plus me-1"></i>Add Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Add Item Form (Initially Hidden) -->
                        <div id="add-item-form" class="border rounded p-3 mb-3 bg-light" style="display: none;">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-plus-circle me-1"></i>Add New Item to Order
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Item Name *</label>
                                    <input type="text" id="new-item-name" class="form-control" placeholder="Enter item name">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Item Code</label>
                                    <input type="text" id="new-item-code" class="form-control" placeholder="Enter item code">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Description</label>
                                    <textarea id="new-item-description" class="form-control" rows="2" placeholder="Enter item description"></textarea>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Quantity *</label>
                                    <input type="number" id="new-item-quantity" min="0.001" step="0.001" class="form-control" placeholder="0">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Unit Price (R) *</label>
                                    <input type="number" id="new-item-price" step="0.01" min="0" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Line Total</label>
                                    <input type="text" id="new-item-total" class="form-control bg-secondary text-white" readonly>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-success" onclick="addItemToList()">
                                    <i class="fas fa-check me-1"></i>Add Item
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="hideAddItemForm()">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Existing Items List -->
                        <div id="items-list">
                            @foreach($purchaseOrder->items as $index => $item)
                                <div class="item-display border rounded p-3 mb-3 bg-white" data-item-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-box me-1"></i>Item {{ $index + 1 }}: {{ $item->item_name }}
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExistingItem({{ $index }})" title="Remove Item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label small">Item Name *</label>
                                                <input type="text" name="items[{{ $index }}][item_name]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $item->item_name }}" required
                                                       onchange="calculateItemTotal({{ $index }})">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Item Code</label>
                                                <input type="text" name="items[{{ $index }}][item_code]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $item->item_code }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label small">Quantity *</label>
                                                <input type="number" name="items[{{ $index }}][quantity_ordered]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $item->quantity_ordered }}" 
                                                       min="0.001" step="0.001" required
                                                       onchange="calculateItemTotal({{ $index }})">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Unit Price (R) *</label>
                                                <input type="number" name="items[{{ $index }}][unit_price]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $item->unit_price }}" 
                                                       min="0" step="0.01" required
                                                       onchange="calculateItemTotal({{ $index }})">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label small">Description</label>
                                            <textarea name="items[{{ $index }}][item_description]" 
                                                      class="form-control form-control-sm" rows="2">{{ $item->item_description }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>Line Total: R <span class="line-total">{{ number_format($item->line_total, 2) }}</span></strong>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden inputs -->
                                    <input type="hidden" name="items[{{ $index }}][inventory_id]" value="{{ $item->inventory_id }}">
                                </div>
                            @endforeach
                        </div>
                        
                        @if($purchaseOrder->items->count() == 0)
                            <div class="alert alert-info" id="no-items-alert">
                                <i class="fas fa-info-circle me-2"></i>
                                No items in this purchase order. Click "Add Item" to add products.
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
                            <span id="subtotal">R {{ number_format($purchaseOrder->total_amount ?? 0, 2) }}</span>
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

                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>Update Purchase Order
                    </button>
                    <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ $purchaseOrder->items->count() }};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit page loaded');
    
    // Add event listeners for new item calculations
    const quantityInput = document.getElementById('new-item-quantity');
    const priceInput = document.getElementById('new-item-price');
    
    if (quantityInput) quantityInput.addEventListener('input', calculateNewItemTotal);
    if (priceInput) priceInput.addEventListener('input', calculateNewItemTotal);
    
    // Calculate initial totals
    calculateOrderTotal();
});

function showAddItemForm() {
    document.getElementById('add-item-form').style.display = 'block';
    document.getElementById('new-item-name').focus();
}

function hideAddItemForm() {
    document.getElementById('add-item-form').style.display = 'none';
    clearAddItemForm();
}

function clearAddItemForm() {
    document.getElementById('new-item-name').value = '';
    document.getElementById('new-item-code').value = '';
    document.getElementById('new-item-description').value = '';
    document.getElementById('new-item-quantity').value = '';
    document.getElementById('new-item-price').value = '';
    document.getElementById('new-item-total').value = '';
}

function calculateNewItemTotal() {
    const quantity = parseFloat(document.getElementById('new-item-quantity').value) || 0;
    const price = parseFloat(document.getElementById('new-item-price').value) || 0;
    const total = quantity * price;
    
    document.getElementById('new-item-total').value = 'R ' + total.toFixed(2);
}

function addItemToList() {
    const itemName = document.getElementById('new-item-name').value.trim();
    const itemCode = document.getElementById('new-item-code').value.trim();
    const description = document.getElementById('new-item-description').value.trim();
    const quantity = parseFloat(document.getElementById('new-item-quantity').value) || 0;
    const price = parseFloat(document.getElementById('new-item-price').value) || 0;
    
    // Validation
    if (!itemName || quantity <= 0 || price < 0) {
        alert('Please fill in all required fields with valid values.');
        return;
    }
    
    const lineTotal = quantity * price;
    
    // Add new item to the list
    const itemsList = document.getElementById('items-list');
    const newItemHTML = `
        <div class="item-display border rounded p-3 mb-3 bg-white" data-item-index="${itemIndex}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 text-primary">
                    <i class="fas fa-box me-1"></i>Item ${itemIndex + 1}: ${itemName}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeNewItem(${itemIndex})" title="Remove Item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label small">Item Name *</label>
                        <input type="text" name="items[${itemIndex}][item_name]" 
                               class="form-control form-control-sm" 
                               value="${itemName}" required
                               onchange="calculateItemTotal(${itemIndex})">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Item Code</label>
                        <input type="text" name="items[${itemIndex}][item_code]" 
                               class="form-control form-control-sm" 
                               value="${itemCode}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label small">Quantity *</label>
                        <input type="number" name="items[${itemIndex}][quantity_ordered]" 
                               class="form-control form-control-sm" 
                               value="${quantity}" 
                               min="0.001" step="0.001" required
                               onchange="calculateItemTotal(${itemIndex})">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Unit Price (R) *</label>
                        <input type="number" name="items[${itemIndex}][unit_price]" 
                               class="form-control form-control-sm" 
                               value="${price}" 
                               min="0" step="0.01" required
                               onchange="calculateItemTotal(${itemIndex})">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <label class="form-label small">Description</label>
                    <textarea name="items[${itemIndex}][item_description]" 
                              class="form-control form-control-sm" rows="2">${description}</textarea>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-6">
                    <small class="text-muted">
                        <strong>Line Total: R <span class="line-total">${lineTotal.toFixed(2)}</span></strong>
                    </small>
                </div>
            </div>
            
            <input type="hidden" name="items[${itemIndex}][inventory_id]" value="">
        </div>
    `;
    
    itemsList.insertAdjacentHTML('beforeend', newItemHTML);
    itemIndex++;
    
    hideAddItemForm();
    calculateOrderTotal();
    
    // Hide no items alert
    const noItemsAlert = document.getElementById('no-items-alert');
    if (noItemsAlert) {
        noItemsAlert.style.display = 'none';
    }
}

function removeExistingItem(index) {
    if (confirm('Are you sure you want to remove this item?')) {
        const itemElement = document.querySelector(`[data-item-index="${index}"]`);
        if (itemElement) {
            itemElement.remove();
            calculateOrderTotal();
        }
    }
}

function removeNewItem(index) {
    if (confirm('Are you sure you want to remove this item?')) {
        const itemElement = document.querySelector(`[data-item-index="${index}"]`);
        if (itemElement) {
            itemElement.remove();
            calculateOrderTotal();
        }
    }
}

function calculateItemTotal(index) {
    const quantityInput = document.querySelector(`input[name="items[${index}][quantity_ordered]"]`);
    const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
    const lineTotalSpan = document.querySelector(`[data-item-index="${index}"] .line-total`);
    
    if (quantityInput && priceInput && lineTotalSpan) {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        
        lineTotalSpan.textContent = total.toFixed(2);
        calculateOrderTotal();
    }
}

function calculateOrderTotal() {
    let subtotal = 0;
    const lineTotals = document.querySelectorAll('.line-total');
    
    lineTotals.forEach(function(element) {
        subtotal += parseFloat(element.textContent) || 0;
    });
    
    const vatAmount = subtotal * 0.15;
    const grandTotal = subtotal + vatAmount;
    
    document.getElementById('subtotal').textContent = 'R ' + subtotal.toFixed(2);
    document.getElementById('vat-amount').textContent = 'R ' + vatAmount.toFixed(2);
    document.getElementById('grand-total').textContent = 'R ' + grandTotal.toFixed(2);
    document.getElementById('total-items').textContent = lineTotals.length;
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

#add-item-form {
    border: 2px dashed #28a745;
    background-color: #d4edda !important;
}
</style>
@endsection