{{-- filepath: resources/views/purchase-orders/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-file-invoice me-2"></i>Purchase Orders
                            </h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>New Purchase Order
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Search and Filters --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('purchase-orders.index') }}">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           value="{{ request('search') }}" 
                                           class="form-control" 
                                           placeholder="Search PO number or supplier...">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="col-md-2">
                            <form method="GET" action="{{ route('purchase-orders.index') }}">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    {{-- Purchase Orders Table --}}
                    @if($purchaseOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Date Created</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrders as $po)
                                        <tr>
                                            <td>
                                                <strong>{{ $po->po_number }}</strong>
                                                @if($po->terms_conditions)
                                                    <br><small class="text-muted">{{ Str::limit($po->terms_conditions, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $po->supplier_name ?: 'Unknown Supplier' }}</strong>
                                                    @if($po->contact_person)
                                                        <br><small class="text-muted">{{ $po->contact_person }}</small>
                                                    @endif
                                                    @if($po->supplier_email)
                                                        <br><small class="text-muted">{{ $po->supplier_email }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{-- Use created_at since order_date doesn't exist --}}
                                                    <strong>{{ $po->created_at ? \Carbon\Carbon::parse($po->created_at)->format('d M Y') : 'N/A' }}</strong>
                                                    <br><small class="text-muted">{{ $po->created_at ? \Carbon\Carbon::parse($po->created_at)->format('H:i') : '' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClasses = [
                                                        'draft' => 'bg-secondary',
                                                        'sent' => 'bg-info',
                                                        'confirmed' => 'bg-warning',
                                                        'completed' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                    ];
                                                    $statusClass = $statusClasses[$po->status ?? 'draft'] ?? 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $statusClass }}">
                                                    {{ ucfirst($po->status ?? 'draft') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('purchase-orders.show', $po->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('purchase-orders.edit', $po->id) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('purchase-orders.pdf', $po->id) }}" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       title="Download PDF" 
                                                       target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $purchaseOrders->firstItem() }} to {{ $purchaseOrders->lastItem() }} 
                                    of {{ $purchaseOrders->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $purchaseOrders->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Purchase Orders Found</h5>
                            <p class="text-muted">Get started by creating your first purchase order.</p>
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create Purchase Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection