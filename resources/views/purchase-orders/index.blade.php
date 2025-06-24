{{-- filepath: resources/views/purchase-orders/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-file-invoice text-primary me-2"></i>
                Purchase Orders
            </h2>
            <p class="text-muted">Manage all purchase orders and supplier orders</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Create New PO
            </a>
            <a href="{{ route('master.settings') }}" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Settings
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('purchase-orders.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="PO Number, Supplier..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="partially_received" {{ request('status') == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
                            <option value="fully_received" {{ request('status') == 'fully_received' ? 'selected' : '' }}>Fully Received</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchase Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Purchase Orders List
                <span class="badge bg-primary ms-2">{{ $purchaseOrders->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($purchaseOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total Amount</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $po)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $po->po_number }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $po->supplier->name ?? 'N/A' }}</strong>
                                            @if($po->supplier)
                                                <br><small class="text-muted">{{ $po->supplier->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $po->order_date->format('d M Y') }}</strong>
                                            @if($po->expected_delivery_date)
                                                <br><small class="text-muted">Expected: {{ $po->expected_delivery_date->format('d M Y') }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'draft' => 'bg-secondary',
                                                'sent' => 'bg-info',
                                                'confirmed' => 'bg-warning',
                                                'partially_received' => 'bg-primary',
                                                'fully_received' => 'bg-success',
                                                'cancelled' => 'bg-danger'
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$po->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>R {{ number_format($po->grand_total, 2) }}</strong>
                                            <br><small class="text-muted">Excl: R {{ number_format($po->total_amount, 2) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $po->createdBy->name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $po->created_at->format('d M Y H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- View Button -->
                                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-primary text-white" title="View Details">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            
                                            <!-- Edit Button (only for draft orders) -->
                                            @if($po->status === 'draft')
                                                <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-warning text-dark" title="Edit Order">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            @endif
                                            
                                            <!-- PDF Button -->
                                            <a href="{{ route('purchase-orders.pdf', $po) }}" class="btn btn-sm btn-danger text-white" title="Download PDF" target="_blank">
                                                <i class="fas fa-file-pdf me-1"></i>PDF
                                            </a>
                                            
                                            <!-- Dropdown for additional actions -->
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if(in_array($po->status, ['sent', 'confirmed', 'partially_received']))
                                                        <li><a class="dropdown-item" href="{{ route('purchase-orders.receive', $po) }}">
                                                            <i class="fas fa-truck me-2 text-success"></i>Receive Goods
                                                        </a></li>
                                                    @endif
                                                    
                                                    @if($po->status === 'draft')
                                                        <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $po->id }}, 'sent')">
                                                            <i class="fas fa-paper-plane me-2 text-info"></i>Send to Supplier
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $po->id }}, 'confirmed')">
                                                            <i class="fas fa-handshake me-2 text-warning"></i>Mark as Confirmed
                                                        </a></li>
                                                    @endif
                                                    
                                                    @if($po->status === 'sent')
                                                        <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $po->id }}, 'confirmed')">
                                                            <i class="fas fa-handshake me-2 text-warning"></i>Mark as Confirmed
                                                        </a></li>
                                                    @endif
                                                    
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#" onclick="window.open('mailto:{{ $po->supplier->email ?? '' }}?subject=Purchase Order {{ $po->po_number }}')">
                                                        <i class="fas fa-envelope me-2 text-primary"></i>Email Supplier
                                                    </a></li>
                                                    
                                                    @if($po->status === 'draft')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteOrder({{ $po->id }})">
                                                            <i class="fas fa-trash me-2"></i>Delete Order
                                                        </a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    {{ $purchaseOrders->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Purchase Orders Found</h5>
                    <p class="text-muted">Get started by creating your first purchase order.</p>
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create First Purchase Order
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection