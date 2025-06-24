{{-- filepath: resources/views/purchase-orders/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>
                Create Purchase Order
            </h2>
            <p class="text-muted">Generate a new purchase order for inventory procurement</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Purchase Orders
            </a>
        </div>
    </div>

    <!-- Purchase Order Form -->
    <form method="POST" action="{{ route('purchase-orders.store') }}" id="purchaseOrderForm">
        @csrf
        <input type="hidden" name="debug_test" value="form_submitted">
        
        <div class="row">
            <!-- Left Column - PO Details -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
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
                                                data-payment-terms="{{ $supplier->payment_terms }}"
                                                data-email="{{ $supplier->email }}"
                                                data-phone="{{ $supplier->phone }}">
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
                                       value="{{ old('order_date', date('Y-m-d')) }}" required>
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
                                       value="{{ old('expected_delivery_date') }}">
                                @error('expected_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-hashtag me-1"></i>PO Number
                                </label>
                                <input type="text" class="form-control bg-light" 
                                       value="Will be auto-generated" readonly>
                                <small class="text-muted">Purchase Order number will be automatically assigned</small>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="notes" class="form-label fw-bold">
                                    <i class="fas fa-sticky-note me-1"></i>Order Notes
                                </label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="form-control @error('notes') is-invalid @enderror" 
                                          placeholder="Additional instructions or notes for this purchase order">{{ old('notes') }}</textarea>
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
                                <i class="fas fa-plus me-1"></i>Add New Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Add Item Form (Initially Hidden) - NO required attributes -->
                        <div id="add-item-form" class="border rounded p-3 mb-3 bg-light" style="display: none;">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-plus-circle me-1"></i>Add New Item
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Item Name *</label>
                                    <input type="text" id="new-item-name" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Item Code</label>
                                    <input type="text" id="new-item-code" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Description</label>
                                    <textarea id="new-item-description" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Quantity *</label>
                                    <input type="number" id="new-item-quantity" min="1" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Unit Price (R) *</label>
                                    <input type="number" id="new-item-price" step="0.01" min="0" class="form-control">
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

                        <!-- Items List -->
                        <div id="items-list">
                            <!-- Added items will appear here -->
                        </div>
                        
                        <div class="alert alert-info" id="no-items-alert">
                            <i class="fas fa-info-circle me-2"></i>
                            No items added yet. Click "Add New Item" to start adding products to this purchase order.
                        </div>
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
                            <span id="subtotal">R 0.00</span>
                        </div>
                        <div class="summary-row d-flex justify-content-between mb-2">
                            <span>VAT (15%):</span>
                            <span id="vat-amount">R 0.00</span>
                        </div>
                        <hr>
                        <div class="summary-row d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="grand-total">R 0.00</span>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="total-items">0</span> item(s) in this order
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-paper-plane me-2"></i>Create Purchase Order
                    </button>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form> <!-- Make sure form is properly closed -->
</div>

<script>
let itemIndex = 0;
let orderItems = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    // Supplier selection handler
    const supplierSelect = document.getElementById('supplier_id');
    if (supplierSelect) {
        supplierSelect.addEventListener('change', function() {
            console.log('Supplier changed:', this.value);
            updateFormState();
        });
    }

    // Add event listeners for new item form calculations
    const quantityInput = document.getElementById('new-item-quantity');
    const priceInput = document.getElementById('new-item-price');
    
    if (quantityInput) quantityInput.addEventListener('input', calculateNewItemTotal);
    if (priceInput) priceInput.addEventListener('input', calculateNewItemTotal);
});

function showAddItemForm() {
    console.log('Show add item form');
    document.getElementById('add-item-form').style.display = 'block';
    document.getElementById('new-item-name').focus();
}

function hideAddItemForm() {
    console.log('Hide add item form');
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
    console.log('Adding item to list');
    
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
    
    itemIndex++;
    const lineTotal = quantity * price;
    
    const newItem = {
        index: itemIndex,
        name: itemName,
        code: itemCode,
        description: description,
        quantity: quantity,
        price: price,
        total: lineTotal
    };
    
    orderItems.push(newItem);
    console.log('Item added:', newItem);
    console.log('Total items:', orderItems.length);
    
    renderItemsList();
    hideAddItemForm();
    calculateOrderTotal();
    updateFormState();
}

function renderItemsList() {
    console.log('Rendering items list');
    const itemsList = document.getElementById('items-list');
    const noItemsAlert = document.getElementById('no-items-alert');
    
    if (orderItems.length === 0) {
        itemsList.innerHTML = '';
        noItemsAlert.style.display = 'block';
        return;
    }
    
    noItemsAlert.style.display = 'none';
    
    itemsList.innerHTML = orderItems.map((item, index) => `
        <div class="item-display border rounded p-3 mb-3 bg-white" data-item-index="${item.index}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 text-primary">
                    <i class="fas fa-box me-1"></i>Item ${index + 1}: ${item.name}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.index})" title="Remove Item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="row small text-muted">
                <div class="col-md-6">
                    ${item.code ? `<div><strong>Code:</strong> ${item.code}</div>` : ''}
                    ${item.description ? `<div><strong>Description:</strong> ${item.description}</div>` : ''}
                </div>
                <div class="col-md-6">
                    <div><strong>Quantity:</strong> ${item.quantity}</div>
                    <div><strong>Unit Price:</strong> R ${item.price.toFixed(2)}</div>
                    <div class="text-success"><strong>Line Total: R ${item.total.toFixed(2)}</strong></div>
                </div>
            </div>
            
            <!-- Hidden form inputs -->
            <input type="hidden" name="items[${index}][item_name]" value="${item.name}">
            <input type="hidden" name="items[${index}][item_code]" value="${item.code}">
            <input type="hidden" name="items[${index}][description]" value="${item.description}">
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
        updateFormState();
    }
}

function calculateOrderTotal() {
    const subtotal = orderItems.reduce((sum, item) => sum + item.total, 0);
    const vatAmount = subtotal * 0.15;
    const grandTotal = subtotal + vatAmount;
    
    document.getElementById('subtotal').textContent = 'R ' + subtotal.toFixed(2);
    document.getElementById('vat-amount').textContent = 'R ' + vatAmount.toFixed(2);
    document.getElementById('grand-total').textContent = 'R ' + grandTotal.toFixed(2);
    document.getElementById('total-items').textContent = orderItems.length;
}

function updateFormState() {
    const hasItems = orderItems.length > 0;
    const hasSupplier = document.getElementById('supplier_id').value !== '';
    
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = !(hasItems && hasSupplier);
        console.log('Form state updated - hasItems:', hasItems, 'hasSupplier:', hasSupplier);
    }
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

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-success {
    background-color: #198754;
    border-color: #198754;
}

#add-item-form {
    border: 2px dashed #28a745;
    background-color: #d4edda !important;
}
</style>
@endsection