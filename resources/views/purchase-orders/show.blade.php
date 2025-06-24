{{-- filepath: resources/views/purchase-orders/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-file-invoice text-primary me-2"></i>
                Purchase Order: {{ $purchaseOrder->po_number }}
            </h2>
            <p class="text-muted">Order Date: {{ $purchaseOrder->order_date->format('d F Y') }}</p>
        </div>
        <!-- Update the header buttons section -->
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                @if($purchaseOrder->status === 'draft')
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning text-white">
                        <i class="fas fa-edit me-2"></i>Edit Order
                    </a>
                @endif
                
                <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="btn btn-danger text-white">
                    <i class="fas fa-file-pdf me-2"></i>Download PDF
                </a>
                
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog me-2"></i>Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('purchase-orders.index') }}">
                            <i class="fas fa-list me-2 text-secondary"></i>Back to List
                        </a></li>
                        @if($purchaseOrder->status !== 'fully_received' && $purchaseOrder->status !== 'cancelled')
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()">
                                <i class="fas fa-print me-2 text-info"></i>Print Order
                            </a></li>
                            <li><a class="dropdown-item" href="mailto:{{ $purchaseOrder->supplier->email ?? '' }}?subject=Purchase Order {{ $purchaseOrder->po_number }}">
                                <i class="fas fa-envelope me-2 text-primary"></i>Email to Supplier
                            </a></li>
                        @endif
                        @if($purchaseOrder->status === 'draft')
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteOrder()">
                                <i class="fas fa-trash me-2"></i>Delete Order
                            </a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Purchase Order Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <!-- Supplier Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Supplier Information</h6>
                            <div class="border-start border-primary ps-3">
                                <strong>{{ $purchaseOrder->supplier->name }}</strong><br>
                                @if($purchaseOrder->supplier->contact_person)
                                    Contact: {{ $purchaseOrder->supplier->contact_person }}<br>
                                @endif
                                @if($purchaseOrder->supplier->email)
                                    Email: {{ $purchaseOrder->supplier->email }}<br>
                                @endif
                                @if($purchaseOrder->supplier->phone)
                                    Phone: {{ $purchaseOrder->supplier->phone }}<br>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success">Order Information</h6>
                            <div class="border-start border-success ps-3">
                                <strong>PO Number:</strong> {{ $purchaseOrder->po_number }}<br>
                                <strong>Order Date:</strong> {{ $purchaseOrder->order_date->format('d F Y') }}<br>
                                @if($purchaseOrder->expected_delivery_date)
                                    <strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date->format('d F Y') }}<br>
                                @endif
                                <strong>Created By:</strong> {{ $purchaseOrder->createdBy->name ?? 'N/A' }}<br>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <h6 class="fw-bold text-info mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->item_name }}</strong>
                                            @if($item->item_code)
                                                <br><small class="text-muted">{{ $item->item_code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->description ?: '-' }}</td>
                                        <td>{{ number_format($item->quantity_ordered) }}</td>
                                        <td>R {{ number_format($item->unit_price, 2) }}</td>
                                        <td>R {{ number_format($item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                    <td><strong>R {{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>VAT (15%):</strong></td>
                                    <td><strong>R {{ number_format($purchaseOrder->vat_amount, 2) }}</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                                    <td><strong>R {{ number_format($purchaseOrder->grand_total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($purchaseOrder->notes)
                        <div class="mt-4">
                            <h6 class="fw-bold text-warning">Notes</h6>
                            <div class="border-start border-warning ps-3">
                                {{ $purchaseOrder->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status and Actions -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks text-primary me-2"></i>Status & Actions
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Current Status with enhanced styling -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Current Status</label>
                        @php
                            $statusConfig = [
                                'draft' => ['class' => 'bg-secondary', 'icon' => 'fas fa-file-alt'],
                                'sent' => ['class' => 'bg-info', 'icon' => 'fas fa-paper-plane'],
                                'confirmed' => ['class' => 'bg-warning text-dark', 'icon' => 'fas fa-check-circle'],
                                'partially_received' => ['class' => 'bg-primary', 'icon' => 'fas fa-truck-loading'],
                                'fully_received' => ['class' => 'bg-success', 'icon' => 'fas fa-check-double'],
                                'cancelled' => ['class' => 'bg-danger', 'icon' => 'fas fa-times-circle']
                            ];
                            $config = $statusConfig[$purchaseOrder->status] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question'];
                        @endphp
                        <div>
                            <span class="badge {{ $config['class'] }} fs-6 px-3 py-2">
                                <i class="{{ $config['icon'] }} me-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Enhanced Status Actions -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Quick Actions</label>
                        <div class="d-grid gap-2">
                            @if($purchaseOrder->status === 'draft')
                                <button class="btn btn-info text-white" onclick="updateStatus('sent')">
                                    <i class="fas fa-paper-plane me-2"></i>Send to Supplier
                                </button>
                                <button class="btn btn-warning text-dark" onclick="updateStatus('confirmed')">
                                    <i class="fas fa-handshake me-2"></i>Mark as Confirmed
                                </button>
                            @endif
                            
                            @if($purchaseOrder->status === 'sent')
                                <button class="btn btn-warning text-dark" onclick="updateStatus('confirmed')">
                                    <i class="fas fa-handshake me-2"></i>Mark as Confirmed
                                </button>
                            @endif
                            
                            @if(in_array($purchaseOrder->status, ['confirmed', 'partially_received']))
                                <a href="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="btn btn-success text-white">
                                    <i class="fas fa-truck me-2"></i>Receive Goods
                                </a>
                            @endif
                            
                            @if(!in_array($purchaseOrder->status, ['fully_received', 'cancelled']))
                                <button class="btn btn-danger text-white" onclick="updateStatus('cancelled')">
                                    <i class="fas fa-ban me-2"></i>Cancel Order
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Enhanced Order Summary -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-chart-bar me-2"></i>Order Summary
                        </h6>
                        <div class="bg-light p-3 rounded">
                            <div class="row text-sm mb-1">
                                <div class="col-7"><i class="fas fa-boxes text-muted me-2"></i>Items:</div>
                                <div class="col-5 text-end fw-bold">{{ $purchaseOrder->items->count() }}</div>
                            </div>
                            <div class="row text-sm mb-1">
                                <div class="col-7"><i class="fas fa-calculator text-muted me-2"></i>Total Qty:</div>
                                <div class="col-5 text-end fw-bold">{{ $purchaseOrder->items->sum('quantity_ordered') }}</div>
                            </div>
                            <div class="row text-sm mb-1">
                                <div class="col-7"><i class="fas fa-money-bill text-muted me-2"></i>Subtotal:</div>
                                <div class="col-5 text-end">R {{ number_format($purchaseOrder->total_amount, 2) }}</div>
                            </div>
                            <div class="row text-sm mb-2">
                                <div class="col-7"><i class="fas fa-percent text-muted me-2"></i>VAT (15%):</div>
                                <div class="col-5 text-end">R {{ number_format($purchaseOrder->vat_amount, 2) }}</div>
                            </div>
                            <div class="row fw-bold border-top pt-2 text-primary">
                                <div class="col-7"><i class="fas fa-coins me-2"></i>Grand Total:</div>
                                <div class="col-5 text-end fs-5">R {{ number_format($purchaseOrder->grand_total, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Goods Received History -->
            @if($purchaseOrder->grvs && $purchaseOrder->grvs->count() > 0)
                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Goods Received History
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($purchaseOrder->grvs as $grv)
                            <div class="border-bottom pb-2 mb-2 last:border-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-success">
                                            <i class="fas fa-receipt me-1"></i>GRV {{ $grv->grv_number }}
                                        </strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>{{ $grv->received_date->format('d M Y') }}
                                        </small><br>
                                        <small class="text-info">
                                            <i class="fas fa-box me-1"></i>{{ $grv->items->count() }} items received
                                        </small>
                                    </div>
                                    <a href="{{ route('grv.show', $grv) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.btn i {
    opacity: 1 !important;
}

.btn:hover i {
    opacity: 1 !important;
}

/* Ensure icons are always visible */
.fas, .far, .fab {
    display: inline-block !important;
}
</style>

<script>
function updateStatus(newStatus) {
    const statusMessages = {
        'sent': 'send this order to the supplier',
        'confirmed': 'mark this order as confirmed',
        'cancelled': 'cancel this order'
    };
    
    const message = statusMessages[newStatus] || `change the status to "${newStatus.replace('_', ' ')}"`;
    
    if (confirm(`Are you sure you want to ${message}?`)) {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        button.disabled = true;
        
        // Use direct URL instead of route helper in JavaScript
        fetch(`/purchase-orders/{{ $purchaseOrder->id }}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                alert(`Order status updated to "${newStatus.replace('_', ' ')}" successfully!`);
                location.reload();
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status: ' + error.message);
            
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

function deleteOrder() {
    if (confirm('Are you sure you want to delete this purchase order? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/purchase-orders/{{ $purchaseOrder->id }}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection