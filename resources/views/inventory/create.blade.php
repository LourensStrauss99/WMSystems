@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Add New Inventory Item</h4>
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.inventory.store') }}" id="inventoryForm">
                        @csrf
                        
                        <!-- Department and Auto-Generated Code -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department *</label>
                                <select name="department" id="department" class="form-control" required onchange="generateInventoryCode()">
                                    <option value="">Select Department</option>
                                    @foreach(\App\Models\Inventory::getDepartmentOptions() as $prefix => $name)
                                        <option value="{{ $prefix }}" {{ old('department') == $prefix ? 'selected' : '' }}>
                                            {{ $prefix }} - {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">This determines the inventory code prefix</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Inventory Code</label>
                                <input type="text" name="short_code" id="short_code" class="form-control" 
                                       value="{{ old('short_code') }}" readonly
                                       placeholder="Select department to generate code">
                                <small class="text-muted">Auto-generated based on department selection</small>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Description *</label>
                                <input type="text" name="description" class="form-control" 
                                       value="{{ old('description') }}" required
                                       placeholder="Enter item description">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vendor/Supplier</label>
                                <input type="text" name="vendor" class="form-control" 
                                       value="{{ old('vendor') }}"
                                       placeholder="Supplier name">
                            </div>
                        </div>

                        <!-- Purchase Documentation Section -->
                        <h5 class="mt-4 mb-3 text-primary border-bottom pb-2">📋 Purchase Documentation</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Invoice Number</label>
                                <input type="text" name="invoice_number" class="form-control" 
                                       value="{{ old('invoice_number') }}"
                                       placeholder="Invoice/receipt number">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purchase Order Number</label>
                                <input type="text" name="purchase_order_number" class="form-control" 
                                       value="{{ old('purchase_order_number') }}"
                                       placeholder="PO number if applicable">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" 
                                       value="{{ old('purchase_date', date('Y-m-d')) }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Goods Received Voucher</label>
                                <input type="text" name="goods_received_voucher" class="form-control" 
                                       value="{{ old('goods_received_voucher') }}"
                                       placeholder="GRV number if applicable">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Purchase Notes</label>
                            <textarea name="purchase_notes" class="form-control" rows="2"
                                      placeholder="Additional purchase information">{{ old('purchase_notes') }}</textarea>
                        </div>

                        <!-- Stock Information -->
                        <h5 class="mt-4 mb-3 text-success border-bottom pb-2">📦 Stock Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Initial Stock Quantity *</label>
                                <input type="number" name="stock_level" class="form-control" 
                                       value="{{ old('stock_level') }}" required min="0"
                                       placeholder="How many units are you adding?">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Stock Level *</label>
                                <input type="number" name="min_quantity" class="form-control" 
                                       value="{{ old('min_quantity') }}" required min="0"
                                       placeholder="Reorder level">
                                <small class="text-muted">System will alert when stock reaches this level</small>
                            </div>
                        </div>

                        <!-- Pricing Information -->
                        <h5 class="mt-4 mb-3 text-warning border-bottom pb-2">💰 Pricing Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Buying Price (Excl. VAT) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input type="number" name="buying_price" id="buying_price" class="form-control" 
                                           value="{{ old('buying_price') }}" required min="0" step="0.01"
                                           placeholder="0.00" onchange="calculateSellingPrice()">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Selling Price (Excl. VAT) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input type="number" name="selling_price" id="selling_price" class="form-control" 
                                           value="{{ old('selling_price') }}" required min="0" step="0.01"
                                           placeholder="0.00">
                                </div>
                                <small class="text-muted">Auto-calculated with markup or enter manually</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock Update Reason</label>
                            <input type="text" name="stock_update_reason" class="form-control" 
                                   value="{{ old('stock_update_reason', 'Initial stock entry') }}"
                                   placeholder="Reason for this stock entry">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Add Inventory Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate inventory code when department is selected
function generateInventoryCode() {
    const departmentSelect = document.getElementById('department');
    const codeInput = document.getElementById('short_code');
    
    if (departmentSelect.value) {
        // Make AJAX request to generate the next available code
        fetch(`/api/inventory/generate-code/${departmentSelect.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    codeInput.value = data.code;
                } else {
                    alert('Error generating inventory code: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating inventory code');
            });
    } else {
        codeInput.value = '';
    }
}

// Auto-calculate selling price based on company markup
function calculateSellingPrice() {
    const buyingPrice = parseFloat(document.getElementById('buying_price').value) || 0;
    const sellingPriceInput = document.getElementById('selling_price');
    
    if (buyingPrice > 0) {
        // Get company markup percentage (default 30% if not available)
        fetch('/api/company/markup-percentage')
            .then(response => response.json())
            .then(data => {
                const markupPercent = data.markup_percentage || 30;
                const sellingPrice = buyingPrice * (1 + (markupPercent / 100));
                sellingPriceInput.value = sellingPrice.toFixed(2);
            })
            .catch(error => {
                // Fallback to 30% markup
                const sellingPrice = buyingPrice * 1.30;
                sellingPriceInput.value = sellingPrice.toFixed(2);
            });
    }
}

// Populate form if creating from existing item (replenishment)
@if(request('item_id'))
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-populate some fields for replenishment
        fetch(`/api/inventory/{{ request('item_id') }}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('description').value = data.item.description + ' (Replenishment)';
                    document.getElementById('vendor').value = data.item.vendor || '';
                    document.getElementById('buying_price').value = data.item.buying_price || '';
                    document.getElementById('selling_price').value = data.item.selling_price || '';
                    document.getElementById('min_quantity').value = data.item.min_quantity || '';
                    
                    // Set as replenishment
                    const form = document.getElementById('inventoryForm');
                    const replenishmentInput = document.createElement('input');
                    replenishmentInput.type = 'hidden';
                    replenishmentInput.name = 'is_replenishment';
                    replenishmentInput.value = '1';
                    form.appendChild(replenishmentInput);
                    
                    const originalItemInput = document.createElement('input');
                    originalItemInput.type = 'hidden';
                    originalItemInput.name = 'original_item_id';
                    originalItemInput.value = '{{ request("item_id") }}';
                    form.appendChild(originalItemInput);
                }
            });
    });
@endif
</script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.border-bottom {
    border-bottom: 2px solid #dee2e6 !important;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

#short_code {
    background-color: #f8f9fa;
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
.text-warning { color: #fd7e14 !important; }
</style>
@endsection
