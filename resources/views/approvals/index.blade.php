{{-- resources/views/approvals/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-clipboard-check me-2"></i>Purchase Orders Pending Approval</h2>
    </div>
    
    @if($pendingOrders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Total Amount</th>
                        <th>Submitted By</th>
                        <th>Submitted Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingOrders as $order)
                        <tr>
                            <td>{{ $order->po_number ?? 'N/A' }}</td>
                            <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                            <td>R {{ number_format($order->grand_total ?? 0, 2) }}</td>
                            <td>{{ $order->submittedBy->name ?? 'N/A' }}</td>
                            <td>{{ $order->submitted_for_approval_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('purchase-orders.show', $order) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye"></i> Review & Approve
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $pendingOrders->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No purchase orders pending approval at this time.
        </div>
    @endif
</div>
@endsection