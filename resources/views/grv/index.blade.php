{{-- filepath: resources/views/grv/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-truck-loading text-primary me-2"></i>
                Goods Received Vouchers (GRV)
            </h2>
            <p class="text-muted">Manage incoming goods and delivery confirmations</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('grv.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Create New GRV
            </a>
        </div>
    </div>

    @if($grvs->count() > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All GRVs
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Search GRVs..." id="searchInput">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>GRV Number</th>
                                <th>Purchase Order</th>
                                <th>Supplier</th>
                                <th>Received Date</th>
                                <th>Received By</th>
                                <th>Overall Status</th>
                                <th>Quality Check</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grvs as $grv)
                                <tr>
                                    <td>
                                        <strong>{{ $grv->grv_number }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $grv->purchaseOrder) }}" class="text-decoration-none">
                                            {{ $grv->purchaseOrder->po_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $grv->purchaseOrder->supplier->name }}</strong>
                                            @if($grv->delivery_company)
                                                <small class="text-muted d-block">via {{ $grv->delivery_company }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $grv->received_date->format('M j, Y') }}
                                            <small class="text-muted d-block">{{ $grv->received_time->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $grv->receivedBy->name }}
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($grv->overall_status == 'complete') bg-success
                                            @elseif($grv->overall_status == 'partial') bg-warning
                                            @elseif($grv->overall_status == 'damaged') bg-danger
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($grv->overall_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($grv->quality_check_passed)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Passed
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('grv.show', $grv) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($grv->canBeApproved())
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="approveGrv({{ $grv->id }})"
                                                        title="Approve GRV">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $grvs->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-truck-loading fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No GRVs Found</h5>
                <p class="text-muted">Start by creating your first Goods Received Voucher.</p>
                <a href="{{ route('grv.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create First GRV
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Approve GRV Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve GRV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this GRV? This will update the inventory levels.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="approveForm">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Approve GRV
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function approveGrv(grvId) {
    document.getElementById('approveForm').action = `/grv/${grvId}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection