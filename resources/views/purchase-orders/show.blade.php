{{-- filepath: resources/views/purchase-orders/show.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Controls (hidden when printing) -->
    <div class="d-print-none mb-4">
        <div class="row">
            <div class="col-md-8">
                <h3 class="text-dark fw-bold">
                    <i class="fas fa-file-invoice text-primary me-2"></i>
                    Purchase Order Details
                </h3>
                <!-- Add Back Button -->
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
                </a>
            </div>
            <div class="col-md-4 text-end">
                @if($purchaseOrder->status === 'draft')
                    <form method="POST" action="{{ route('purchase-orders.submit-for-approval', $purchaseOrder) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-warning">
                            <i class="fas fa-paper-plane me-1"></i>Submit for Approval
                        </button>
                    </form>
                @endif

                @if($purchaseOrder->status == 'pending_approval' && auth()->user()->canApprove())
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this purchase order?')">
                                <i class="fas fa-check me-1"></i>Approve
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-1"></i>Reject
                        </button>
                    </div>
                @endif

                @if($purchaseOrder->status === 'approved')
                    <form method="POST" action="{{ route('purchase-orders.send', $purchaseOrder) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-primary">
                            <i class="fas fa-envelope me-1"></i>Send to Supplier
                        </button>
                    </form>
                @endif

                @if($purchaseOrder->canCreateGrv())
                    <a href="{{ route('grv.create', ['purchase_order_id' => $purchaseOrder->id]) }}" class="btn btn-warning">
                        <i class="fas fa-truck me-1"></i>Create GRV
                    </a>
                @endif
            </div>
        </div>
        <hr>
    </div>

    <!-- PDF/Print Content -->
    <div class="print-content">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-6">
                <h1 class="company-name">Your Company Name</h1>
                <p class="company-details mb-0">
                    123 Business Street<br>
                    City, Province, 0000<br>
                    Tel: +27 11 123 4567<br>
                    Email: orders@yourcompany.com
                </p>
            </div>
            <div class="col-6 text-end">
                <h2 class="document-title">PURCHASE ORDER</h2>
                <div class="po-details">
                    <strong>PO Number:</strong> {{ $purchaseOrder->po_number ?? '-' }}<br>
                    <strong>Date:</strong> {{ $purchaseOrder->created_at ? $purchaseOrder->created_at->format('d F Y') : '-' }}<br>
                    <strong>Status:</strong>
                    <span class="badge bg-{{ $purchaseOrder->status === 'draft' ? 'primary' : ($purchaseOrder->status === 'pending_approval' ? 'warning' : ($purchaseOrder->status === 'approved' ? 'success' : 'secondary')) }}">
                        {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        <!-- Supplier Information -->
        <div class="row mb-4">
            <div class="col-6">
                <h4 class="section-title">Ship To:</h4>
                <div class="address-box">
                    <strong>Your Company Name</strong><br>
                    123 Business Street<br>
                    City, Province, 0000<br>
                    South Africa
                </div>
            </div>
            <div class="col-6">
                <h4 class="section-title">Supplier:</h4>
                <div class="address-box">
                    @if($purchaseOrder->supplier)
                        <strong>{{ $purchaseOrder->supplier->name }}</strong><br>
                        @if($purchaseOrder->supplier->contact_person)
                            Attn: {{ $purchaseOrder->supplier->contact_person }}<br>
                        @endif
                        @if($purchaseOrder->supplier->address)
                            {{ $purchaseOrder->supplier->address }}<br>
                        @endif
                        @if($purchaseOrder->supplier->email)
                            Email: {{ $purchaseOrder->supplier->email }}<br>
                        @endif
                        @if($purchaseOrder->supplier->phone)
                            Tel: {{ $purchaseOrder->supplier->phone }}<br>
                        @endif
                    @else
                        <strong>No supplier</strong>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="mb-4">
            <h4 class="section-title">Order Details</h4>
            <table class="table table-bordered items-table">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" width="5%">#</th>
                        <th width="20%">Item Description</th>
                        <th class="text-center" width="10%">Item Code</th>
                        <th class="text-center" width="8%">Ordered</th>
                        <th class="text-center" width="8%">Received</th>
                        <th class="text-center" width="8%">Outstanding</th>
                        <th class="text-center" width="10%">Unit Price</th>
                        <th class="text-center" width="12%">Line Total</th>
                        <th class="text-center" width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrder->items as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $item->item_description ?? $item->item_name }}</td>
                            <td class="text-center">{{ $item->item_code }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ number_format($item->quantity_ordered) }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->quantity_received > 0)
                                    <span class="badge bg-success">{{ number_format($item->quantity_received) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $outstanding = $item->quantity_ordered - ($item->quantity_received ?? 0);
                                @endphp
                                @if($outstanding > 0)
                                    <span class="badge bg-warning">{{ number_format($outstanding) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">R {{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-center">R {{ number_format($item->line_total, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $item->status === 'fully_received' ? 'success' : ($item->status === 'partially_received' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status ?? 'pending')) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No items found for this order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="row mb-4">
            <div class="col-6">
                @if($purchaseOrder->terms_conditions)
                    <h5 class="section-title">Special Instructions:</h5>
                    <div class="terms-box">
                        {{ $purchaseOrder->terms_conditions }}
                    </div>
                @endif

                <h5 class="section-title mt-3">Payment Terms:</h5>
                <div class="terms-box">
                    {{ $purchaseOrder->payment_terms ?? '30 days from invoice date' }}
                </div>
            </div>
            <div class="col-6">
                <div class="totals-section">
                    <table class="table table-borderless totals-table">
                        <tr>
                            <td class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end amount">R {{ number_format($purchaseOrder->total_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>VAT (15%):</strong></td>
                            <td class="text-end amount">R {{ number_format($purchaseOrder->vat_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td class="text-end"><strong>TOTAL:</strong></td>
                            <td class="text-end total-amount"><strong>R {{ number_format($purchaseOrder->grand_total ?? 0, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Related GRVs Section -->
        @if($purchaseOrder->grvs->count() > 0)
            <div class="mb-4">
                <h4 class="section-title">Related GRVs</h4>
                <div class="mb-2">
                    @foreach($purchaseOrder->grvs as $grv)
                        <a href="{{ route('grv.show', $grv->id) }}" class="btn btn-sm btn-outline-info me-2 mb-1">
                            {{ $grv->grv_number }} 
                            <span class="badge bg-secondary">{{ ucfirst($grv->overall_status) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Delivery Progress Section -->
        @if($purchaseOrder->grvs->count() > 0)
            <div class="mb-4">
                <h4 class="section-title">Delivery Progress</h4>
                @php
                    $totalOrdered = $purchaseOrder->items->sum('quantity_ordered');
                    $totalReceived = $purchaseOrder->items->sum('quantity_received');
                    $completionPercent = $totalOrdered > 0 ? round(($totalReceived / $totalOrdered) * 100) : 0;
                @endphp
                
                <div class="progress mb-2">
                    <div class="progress-bar 
                        @if($completionPercent == 100) bg-success
                        @elseif($completionPercent >= 50) bg-warning  
                        @else bg-danger
                        @endif" 
                         style="width: {{ $completionPercent }}%">
                        {{ $completionPercent }}%
                    </div>
                </div>
                
                <small class="text-muted">
                    Received: {{ number_format($totalReceived) }} / {{ number_format($totalOrdered) }} items
                </small>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer-section mt-5 pt-4">
            <div class="text-center mt-4 footer-text">
                <small>Purchase Order</small>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('purchase-orders.reject', $purchaseOrder->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        Reject Purchase Order
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>PO Number:</strong> {{ $purchaseOrder->po_number }}
                    </div>
                    
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            <strong>Reason for Rejection <span class="text-danger">*</span></strong>
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" 
                                  class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  rows="4" required 
                                  placeholder="Please provide a detailed reason for rejecting this purchase order..."></textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        This reason will be communicated to the person who created the order so they can make necessary adjustments.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>Reject Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Keep your existing CSS styles --}}
<style>
/* All your existing styles remain the same */
.company-name {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 10px;
}

.company-details {
    font-size: 14px;
    color: #666;
    line-height: 1.4;
}

.document-title {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 15px;
}

.po-details {
    font-size: 14px;
    line-height: 1.6;
}

.section-divider {
    border-top: 2px solid #2c3e50;
    margin: 20px 0;
}

.section-title {
    font-size: 16px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 10px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.address-box {
    background-color: #f8f9fa;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    font-size: 14px;
    line-height: 1.5;
}

.footer-section {
    border-top: 1px solid #ddd;
}

.footer-text {
    color: #666;
    font-style: italic;
}

/* Screen-only styles */
@media screen {
    .print-content {
        background: white;
        padding: 30px;
        margin: 0 auto;
        max-width: 8.5in;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
}
</style>