@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Edit Inventory Item: {{ $item->description }}</h4>
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

                    <form method="POST" action="/inventory/{{ $item->id }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Department and Code Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department *</label>
                                <select name="department" id="department" class="form-control" required>
                                    <option value="">Select Department</option>
                                    @foreach(\App\Models\Inventory::getDepartmentOptions() as $prefix => $name)
                                        <option value="{{ $prefix }}" {{ old('department', $item->department) == $prefix ? 'selected' : '' }}>
                                            {{ $prefix }} - {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Department category for this inventory item</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Inventory Code *</label>
                                <input type="text" name="short_code" class="form-control" 
                                       value="{{ old('short_code', $item->short_code) }}" required
                                       style="font-family: 'Courier New', monospace; font-weight: bold;">
                                <small class="text-muted">Unique inventory identifier</small>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Description *</label>
                                <input type="text" name="description" class="form-control" 
                                       value="{{ old('description', $item->description) }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vendor/Supplier</label>
                                <input type="text" name="vendor" class="form-control" 
                                       value="{{ old('vendor', $item->vendor) }}">
                            </div>
                        </div>

                        <!-- Purchase Documentation Section -->
                        <h5 class="mt-4 mb-3 text-primary border-bottom pb-2">📋 Purchase Documentation</h5
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Invoice Number</label>
                                <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number', $item->invoice_number) }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Receipt Number</label>
                                <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number', $item->receipt_number) }}">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', $item->purchase_date ? $item->purchase_date->format('Y-m-d') : '') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purchase Order Number</label>
                                <input type="text" name="purchase_order_number" class="form-control" value="{{ old('purchase_order_number', $item->purchase_order_number) }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Goods Received Voucher</label>
                            <input type="text" name="goods_received_voucher" class="form-control" value="{{ old('goods_received_voucher', $item->goods_received_voucher) }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Purchase Notes</label>
                            <textarea name="purchase_notes" class="form-control" rows="2">{{ old('purchase_notes', $item->purchase_notes) }}</textarea>
                        </div>

                        <!-- Show current stock history -->
                        @if($item->last_stock_update)
                            <div class="alert alert-info">
                                <strong>📊 Stock History:</strong><br>
                                Last Updated: {{ $item->last_stock_update }}<br>
                                Last Added: {{ $item->stock_added ?? 0 }} units<br>
                                Reason: {{ $item->stock_update_reason ?? 'N/A' }}
                            </div>
                        @endif

                        <!-- Pricing Section -->
                        <h5 class="mt-4 mb-3 text-success border-bottom pb-2">💰 Pricing Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Buying Price (R) *</label>
                                <input type="number" step="0.01" name="buying_price" class="form-control" 
                                       value="{{ old('buying_price', $item->buying_price) }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Selling Price (R) *</label>
                                <input type="number" step="0.01" name="selling_price" class="form-control" 
                                       value="{{ old('selling_price', $item->selling_price) }}" required>
                            </div>
                        </div>

                        <!-- Stock Information Section -->
                        <h5 class="mt-4 mb-3 text-warning border-bottom pb-2">📦 Stock Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Stock Quantity *</label>
                                <input type="number" name="quantity" class="form-control" 
                                       value="{{ old('quantity', $item->quantity) }}" required min="0">
                                <small class="text-muted">Current number of items in stock</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Quantity *</label>
                                <input type="number" name="min_quantity" class="form-control" 
                                       value="{{ old('min_quantity', $item->min_quantity) }}" required min="0">
                                <small class="text-muted">Minimum stock level before reorder</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/inventory" class="btn btn-secondary">← Back to Inventory</a>
                            <button type="submit" class="btn btn-primary">💾 Update Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection