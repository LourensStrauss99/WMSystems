@extends('layouts.app')
@extends('layouts.nav')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header with breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/inventory">Inventory</a></li>
                    <li class="breadcrumb-item active">{{ $item->name }}</li>
                </ol>
            </nav>

            <!-- Main Item Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-box me-2"></i>{{ $item->name }}
                        </h4>
                        <div>
                            @php $stockStatus = $item->getStockStatus(); @endphp
                            <span class="badge {{ $stockStatus['class'] }} me-2">
                                {{ $stockStatus['icon'] }} {{ $stockStatus['status'] }}
                            </span>
                            <span class="badge bg-light text-dark">{{ $item->short_code }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column - Basic Info -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                            
                            <div class="row mb-3">
                                <div class="col-4"><strong>Name:</strong></div>
                                <div class="col-8">{{ $item->name }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-4"><strong>Code:</strong></div>
                                <div class="col-8">
                                    <span class="badge bg-secondary">{{ $item->short_code }}</span>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-4"><strong>Description:</strong></div>
                                <div class="col-8">{{ $item->description ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-4"><strong>Short Description:</strong></div>
                                <div class="col-8">{{ $item->short_description ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-4"><strong>Supplier:</strong></div>
                                <div class="col-8">{{ $item->supplier ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-4"><strong>Vendor:</strong></div>
                                <div class="col-8">{{ $item->vendor ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <!-- Right Column - Stock & Pricing -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-warehouse me-2"></i>Stock & Pricing
                            </h5>
                            
                            <!-- Stock Level Alert -->
                            <div class="alert alert-{{ $item->isOutOfStock() ? 'danger' : ($item->isAtMinLevel() ? 'warning' : 'success') }} mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Current Stock Level</h6>
                                        <h3 class="mb-0">{{ $item->stock_level }}</h3>
                                    </div>
                                    <div class="text-end">
                                        <div>{{ $stockStatus['icon'] }}</div>
                                        <small>{{ $stockStatus['status'] }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6"><strong>Minimum Level:</strong></div>
                                <div class="col-6">
                                    <span class="badge bg-warning">{{ $item->min_level }}</span>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6"><strong>Buying Price:</strong></div>
                                <div class="col-6">
                                    <span class="text-success">R{{ number_format($item->buying_price, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6"><strong>Selling Price:</strong></div>
                                <div class="col-6">
                                    <span class="text-primary">R{{ number_format($item->selling_price, 2) }}</span>
                                </div>
                            </div>
                            
                            @if($item->buying_price > 0)
                            <div class="row mb-3">
                                <div class="col-6"><strong>Profit Margin:</strong></div>
                                <div class="col-6">
                                    @php
                                        $margin = (($item->selling_price - $item->buying_price) / $item->buying_price) * 100;
                                    @endphp
                                    <span class="badge bg-{{ $margin > 20 ? 'success' : ($margin > 10 ? 'warning' : 'danger') }}">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Purchase Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-5"><strong>Purchase Date:</strong></div>
                                <div class="col-7">
                                    {{ $item->purchase_date ? $item->purchase_date->format('d M Y') : 'N/A' }}
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-5"><strong>Invoice Number:</strong></div>
                                <div class="col-7">{{ $item->invoice_number ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-5"><strong>Receipt Number:</strong></div>
                                <div class="col-7">{{ $item->receipt_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-5"><strong>PO Number:</strong></div>
                                <div class="col-7">{{ $item->purchase_order_number ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-5"><strong>GRV Number:</strong></div>
                                <div class="col-7">{{ $item->goods_received_voucher ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($item->purchase_notes)
                    <div class="row">
                        <div class="col-12">
                            <strong>Purchase Notes:</strong>
                            <div class="bg-light p-3 rounded mt-2">
                                {{ $item->purchase_notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stock History -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Stock History
                    </h5>
                </div>
                <div class="card-body">
                    @if($item->last_stock_update)
                    <div class="row mb-3">
                        <div class="col-4"><strong>Last Updated:</strong></div>
                        <div class="col-8">{{ $item->last_stock_update->format('d M Y H:i') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-4"><strong>Last Added:</strong></div>
                        <div class="col-8">
                            <span class="badge bg-success">+{{ $item->stock_added ?? 0 }}</span> units
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-4"><strong>Update Reason:</strong></div>
                        <div class="col-8">{{ $item->stock_update_reason ?? 'N/A' }}</div>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No stock update history available.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="/inventory" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                            </a>
                        </div>
                        
                        <div>
                            <a href="/inventory/{{ $item->id }}/edit" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Edit Item
                            </a>
                            
                            <button class="btn btn-info" onclick="printItem()">
                                <i class="fas fa-print me-2"></i>Print Details
                            </button>
                            
                            @if($item->isAtMinLevel())
                            <button class="btn btn-primary" onclick="createPurchaseOrder()">
                                <i class="fas fa-shopping-cart me-2"></i>Create Purchase Order
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .btn, .breadcrumb, .card-header {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .container {
        max-width: 100% !important;
    }
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    border-bottom: 2px solid rgba(255,255,255,0.2);
}

.alert {
    border: none;
    border-radius: 8px;
}

.badge {
    font-size: 0.85em;
}
</style>

<script>
function printItem() {
    window.print();
}

function createPurchaseOrder() {
    // Redirect to create purchase order with this item pre-filled
    const url = new URL('/purchase-orders/create', window.location.origin);
    url.searchParams.set('item_id', '{{ $item->id }}');
    url.searchParams.set('suggested_qty', '{{ $item->min_level * 2 }}');
    window.location.href = url.toString();
}

// Auto-refresh stock level every 30 seconds
setInterval(function() {
    // You can add AJAX call here to update stock level in real-time
    // fetch(`/inventory/{{ $item->id }}/status`)...
}, 30000);
</script>
@endsection