{{-- filepath: resources/views/grv/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold">
                <i class="fas fa-truck-loading me-2 text-success"></i>
                Goods Received Voucher (GRV)
            </h2>
            <p class="text-muted">
                GRV Number: <strong>{{ $grv->grv_number }}</strong> | 
                PO: <strong>{{ $grv->purchaseOrder->po_number }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('grv.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i>All GRVs
                </a>
                <a href="{{ route('purchase-orders.show', $grv->purchaseOrder) }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-invoice me-1"></i>View PO
                </a>
                <button class="btn btn-success" onclick="printGRV()">
                    <i class="fas fa-print me-1"></i>Print
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- GRV Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>GRV Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">GRV Number:</td>
                                    <td>{{ $grv->grv_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Received Date:</td>
                                    <td>{{ $grv->received_date->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Received Time:</td>
                                    <td>{{ $grv->received_time->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Received By:</td>
                                    <td>{{ $grv->receivedBy->name }}</td>
                                </tr>
                                @if($grv->checked_by)
                                <tr>
                                    <td class="fw-bold">Checked By:</td>
                                    <td>{{ $grv->checkedBy->name }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($grv->delivery_note_number)
                                <tr>
                                    <td class="fw-bold">Delivery Note:</td>
                                    <td>{{ $grv->delivery_note_number }}</td>
                                </tr>
                                @endif
                                @if($grv->vehicle_registration)
                                <tr>
                                    <td class="fw-bold">Vehicle:</td>
                                    <td>{{ $grv->vehicle_registration }}</td>
                                </tr>
                                @endif
                                @if($grv->driver_name)
                                <tr>
                                    <td class="fw-bold">Driver:</td>
                                    <td>{{ $grv->driver_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Overall Status:</td>
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
                                </tr>
                                <tr>
                                    <td class="fw-bold">Quality Check:</td>
                                    <td>
                                        @if($grv->quality_check_passed)
                                            <span class="badge bg-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($grv->general_notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="fw-bold">General Notes:</h6>
                            <p class="bg-light p-3 rounded">{{ $grv->general_notes }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($grv->discrepancies)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold text-warning">Discrepancies:</h6>
                            <p class="bg-warning bg-opacity-10 p-3 rounded border-start border-warning border-3">
                                {{ $grv->discrepancies }}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($grv->quality_notes)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold text-info">Quality Notes:</h6>
                            <p class="bg-info bg-opacity-10 p-3 rounded border-start border-info border-3">
                                {{ $grv->quality_notes }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Purchase Order Reference -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Purchase Order Reference
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ $grv->purchaseOrder->supplier->name }}</h6>
                            <p class="mb-1">PO Number: {{ $grv->purchaseOrder->po_number }}</p>
                            <p class="mb-1">Order Date: {{ $grv->purchaseOrder->order_date ? $grv->purchaseOrder->order_date->format('F j, Y') : 'Not set' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">Total Value: R {{ number_format($grv->purchaseOrder->grand_total, 2) }}</p>
                            <p class="mb-1">Status: {{ ucfirst($grv->purchaseOrder->status) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Received -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>Items Received
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Ordered</th>
                                    <th class="text-center">Received</th>
                                    <th class="text-center">Rejected</th>
                                    <th class="text-center">Damaged</th>
                                    <th class="text-center">Accepted</th>
                                    <th>Condition</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grv->items as $item)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $item->purchaseOrderItem->item_name }}</strong>
                                                @if($item->purchaseOrderItem->item_code)
                                                    <small class="text-muted d-block">{{ $item->purchaseOrderItem->item_code }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ number_format($item->quantity_ordered) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ number_format($item->quantity_received) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($item->quantity_rejected > 0)
                                                <span class="badge bg-danger">{{ number_format($item->quantity_rejected) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->quantity_damaged > 0)
                                                <span class="badge bg-warning">{{ number_format($item->quantity_damaged) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ number_format($item->getAcceptedQuantity()) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($item->condition == 'good') bg-success
                                                @elseif($item->condition == 'damaged') bg-danger
                                                @elseif($item->condition == 'expired') bg-warning
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($item->condition) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->item_notes)
                                                <small>{{ $item->item_notes }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    
                                    @if($item->quantity_rejected > 0 && $item->rejection_reason)
                                        <tr class="bg-light">
                                            <td colspan="8">
                                                <div class="p-2">
                                                    <small class="fw-bold text-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Rejection Reason:
                                                    </small>
                                                    <small class="d-block">{{ $item->rejection_reason }}</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions -->
            @if($grv->canBeApproved())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-check-circle me-2"></i>Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('grv.approve', $grv) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2" 
                                    onclick="return confirm('Are you sure you want to approve this GRV? This will update inventory levels.')">
                                <i class="fas fa-check me-1"></i>Approve GRV
                            </button>
                        </form>
                        
                        @if(!$grv->quality_check_passed)
                            <form method="POST" action="{{ route('grv.quality-pass', $grv) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-success w-100 mb-2">
                                    <i class="fas fa-check-double me-1"></i>Pass Quality Check
                                </button>
                            </form>
                            
                            <button type="button" class="btn btn-outline-danger w-100" 
                                    data-bs-toggle="modal" data-bs-target="#qualityFailModal">
                                <i class="fas fa-times me-1"></i>Fail Quality Check
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Receipt Summary
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalOrdered = $grv->items->sum('quantity_ordered');
                        $totalReceived = $grv->items->sum('quantity_received');
                        $totalAccepted = $grv->items->sum(function($item) { return $item->getAcceptedQuantity(); });
                        $totalRejected = $grv->items->sum('quantity_rejected');
                        $totalDamaged = $grv->items->sum('quantity_damaged');
                        $completionPercent = $totalOrdered > 0 ? round(($totalReceived / $totalOrdered) * 100) : 0;
                    @endphp
                    
                    <div class="mb-2">
                        <strong>Total Ordered:</strong>
                        <span class="float-end">{{ number_format($totalOrdered) }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Total Received:</strong>
                        <span class="float-end">{{ number_format($totalReceived) }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Total Accepted:</strong>
                        <span class="float-end text-success">{{ number_format($totalAccepted) }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Total Rejected:</strong>
                        <span class="float-end text-danger">{{ number_format($totalRejected) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Total Damaged:</strong>
                        <span class="float-end text-warning">{{ number_format($totalDamaged) }}</span>
                    </div>
                    
                    <div class="mb-2">
                        <strong>Completion:</strong>
                        <span class="float-end">{{ $completionPercent }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar 
                            @if($completionPercent == 100) bg-success
                            @elseif($completionPercent >= 50) bg-warning  
                            @else bg-danger
                            @endif" 
                             style="width: {{ $completionPercent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">PO Created</h6>
                                <small class="text-muted">{{ $grv->purchaseOrder->created_at->format('M j, Y H:i') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Goods Received</h6>
                                <small class="text-muted">{{ $grv->created_at->format('M j, Y H:i') }}</small>
                            </div>
                        </div>
                        
                        @if($grv->checked_by)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Quality Checked</h6>
                                <small class="text-muted">{{ $grv->updated_at->format('M j, Y H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quality Fail Modal -->
<div class="modal fade" id="qualityFailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('grv.quality-fail', $grv) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Fail Quality Check</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quality_notes" class="form-label">Quality Notes <span class="text-danger">*</span></label>
                        <textarea name="quality_notes" id="quality_notes" class="form-control" rows="4" required
                                  placeholder="Please provide detailed notes about why the quality check failed..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Fail Quality Check
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function printGRV() {
    window.print();
}
</script>

<style>
@media print {
    .btn, .btn-group, .card-header {
        display: none !important;
    }
    
    .container {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    font-size: 0.9rem;
    margin-bottom: 2px;
}

.progress {
    height: 20px;
}
</style>
@endsection