{{-- filepath: resources/views/grv/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-plus text-primary me-2"></i>
                Create New GRV
            </h2>
            <p class="text-muted">Create a new Goods Received Voucher</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('grv.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to GRV List
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('grv.store') }}" id="createGrvForm">
        @csrf
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Purchase Order Selection -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Purchase Order Selection
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="purchase_order_id" class="form-label fw-bold">
                                    Select Purchase Order <span class="text-danger">*</span>
                                </label>
                                <select name="purchase_order_id" id="purchase_order_id" 
                                        class="form-select @error('purchase_order_id') is-invalid @enderror" 
                                        required onchange="loadPurchaseOrder()">
                                    <option value="">Choose a Purchase Order...</option>
                                    @if($purchaseOrder)
                                        <option value="{{ $purchaseOrder->id }}" selected>
                                            {{ $purchaseOrder->po_number }} - {{ $purchaseOrder->supplier->name }}
                                        </option>
                                    @endif
                                    @foreach($availablePOs as $po)
                                        @if(!$purchaseOrder || $po->id !== $purchaseOrder->id)
                                            <option value="{{ $po->id }}">
                                                {{ $po->po_number }} - {{ $po->supplier->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('purchase_order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- PO Details (populated by JavaScript) -->
                        <div id="po-details" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Purchase Order Details</h6>
                                    <p class="mb-1"><strong>PO Number:</strong> <span id="po-number"></span></p>
                                    <p class="mb-1"><strong>Supplier:</strong> <span id="po-supplier"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Delivery Information</h6>
                                    <p class="mb-1"><strong>Expected Items:</strong> <span id="po-items-count"></span></p>
                                    <p class="mb-1"><strong>Total Value:</strong> <span id="po-total"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-truck me-2"></i>Delivery Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="received_date" class="form-label fw-bold">
                                    Received Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="received_date" id="received_date" 
                                       class="form-control @error('received_date') is-invalid @enderror" 
                                       value="{{ old('received_date', date('Y-m-d')) }}" required>
                                @error('received_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="received_time" class="form-label fw-bold">
                                    Received Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="received_time" id="received_time" 
                                       class="form-control @error('received_time') is-invalid @enderror" 
                                       value="{{ old('received_time', date('H:i')) }}" required>
                                @error('received_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="delivery_note_number" class="form-label fw-bold">
                                    Delivery Note Number
                                </label>
                                <input type="text" name="delivery_note_number" id="delivery_note_number" 
                                       class="form-control @error('delivery_note_number') is-invalid @enderror" 
                                       value="{{ old('delivery_note_number') }}" 
                                       placeholder="Enter delivery note number">
                                @error('delivery_note_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="vehicle_registration" class="form-label fw-bold">
                                    Vehicle Registration
                                </label>
                                <input type="text" name="vehicle_registration" id="vehicle_registration" 
                                       class="form-control @error('vehicle_registration') is-invalid @enderror" 
                                       value="{{ old('vehicle_registration') }}" 
                                       placeholder="Enter vehicle registration">
                                @error('vehicle_registration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="driver_name" class="form-label fw-bold">
                                    Driver Name
                                </label>
                                <input type="text" name="driver_name" id="driver_name" 
                                       class="form-control @error('driver_name') is-invalid @enderror" 
                                       value="{{ old('driver_name') }}" 
                                       placeholder="Enter driver name">
                                @error('driver_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="delivery_company" class="form-label fw-bold">
                                    Delivery Company
                                </label>
                                <input type="text" name="delivery_company" id="delivery_company" 
                                       class="form-control @error('delivery_company') is-invalid @enderror" 
                                       value="{{ old('delivery_company') }}" 
                                       placeholder="Enter delivery company">
                                @error('delivery_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Received -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-boxes me-2"></i>Items Received
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="items-container">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select a Purchase Order first to load the items.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Overall Status -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Overall Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="overall_status" class="form-label fw-bold">
                                Overall Status <span class="text-danger">*</span>
                            </label>
                            <select name="overall_status" id="overall_status" 
                                    class="form-select @error('overall_status') is-invalid @enderror" required>
                                <option value="">Select Status...</option>
                                <option value="complete" {{ old('overall_status') == 'complete' ? 'selected' : '' }}>Complete</option>
                                <option value="partial" {{ old('overall_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="damaged" {{ old('overall_status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="rejected" {{ old('overall_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('overall_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="quality_check_passed" 
                                       id="quality_check_passed" value="1" 
                                       {{ old('quality_check_passed') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="quality_check_passed">
                                    Quality Check Passed
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="delivery_note_received" 
                                       id="delivery_note_received" value="1" 
                                       {{ old('delivery_note_received') ? 'checked' : '' }}>
                                <label class="form-check-label" for="delivery_note_received">
                                    Delivery Note Received
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="invoice_received" 
                                       id="invoice_received" value="1" 
                                       {{ old('invoice_received') ? 'checked' : '' }}>
                                <label class="form-check-label" for="invoice_received">
                                    Invoice Received
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="general_notes" class="form-label fw-bold">
                                General Notes
                            </label>
                            <textarea name="general_notes" id="general_notes" rows="3" 
                                      class="form-control @error('general_notes') is-invalid @enderror"
                                      placeholder="Any general notes about the delivery">{{ old('general_notes') }}</textarea>
                            @error('general_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="discrepancies" class="form-label fw-bold">
                                Discrepancies
                            </label>
                            <textarea name="discrepancies" id="discrepancies" rows="3" 
                                      class="form-control @error('discrepancies') is-invalid @enderror"
                                      placeholder="Any discrepancies found">{{ old('discrepancies') }}</textarea>
                            @error('discrepancies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quality_notes" class="form-label fw-bold">
                                Quality Notes
                            </label>
                            <textarea name="quality_notes" id="quality_notes" rows="3" 
                                      class="form-control @error('quality_notes') is-invalid @enderror"
                                      placeholder="Quality check notes">{{ old('quality_notes') }}</textarea>
                            @error('quality_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-save me-2"></i>Create GRV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let currentItems = [];

function loadPurchaseOrder() {
    const poId = document.getElementById('purchase_order_id').value;
    if (!poId) {
        document.getElementById('po-details').style.display = 'none';
        document.getElementById('items-container').innerHTML = 
            '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Please select a Purchase Order first to load the items.</div>';
        return;
    }

    fetch(`/api/purchase-orders/${poId}/details`)
        .then(response => response.json())
        .then(data => {
            // Update PO details
            document.getElementById('po-number').textContent = data.po_number;
            document.getElementById('po-supplier').textContent = data.supplier;
            document.getElementById('po-items-count').textContent = data.items.length;
            document.getElementById('po-details').style.display = 'block';

            // Load items
            currentItems = data.items;
            renderItems();
        })
        .catch(error => {
            console.error('Error loading purchase order:', error);
            alert('Error loading purchase order details');
        });
}

function renderItems() {
    const container = document.getElementById('items-container');
    if (currentItems.length === 0) {
        container.innerHTML = '<div class="alert alert-warning">No items found for this purchase order.</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr>' +
        '<th>Item</th><th>Ordered</th><th>Received</th><th>Rejected</th><th>Damaged</th><th>Condition</th><th>Additional Info</th>' +
        '</tr></thead><tbody>';

    currentItems.forEach((item, index) => {
        html += `
            <tr>
                <td>
                    <strong>${item.name}</strong>
                    ${item.code ? `<br><small class="text-muted">${item.code}</small>` : ''}
                    <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                </td>
                <td>
                    <span class="badge bg-secondary">${item.quantity_ordered}</span>
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity_received]" 
                           class="form-control" min="0" max="${item.outstanding}" 
                           value="${item.outstanding}" required>
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity_rejected]" 
                           class="form-control" min="0" value="0" required>
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity_damaged]" 
                           class="form-control" min="0" value="0" required>
                </td>
                <td>
                    <select name="items[${index}][condition]" class="form-select" required>
                        <option value="good">Good</option>
                        <option value="damaged">Damaged</option>
                        <option value="defective">Defective</option>
                        <option value="expired">Expired</option>
                    </select>
                </td>
                <td>
                    <div class="mb-2">
                        <input type="text" name="items[${index}][batch_number]" 
                               class="form-control form-control-sm" 
                               placeholder="Batch Number">
                    </div>
                    <div class="mb-2">
                        <input type="date" name="items[${index}][expiry_date]" 
                               class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <input type="text" name="items[${index}][storage_location]" 
                               class="form-control form-control-sm" 
                               placeholder="Storage Location">
                    </div>
                    <div class="mb-2">
                        <textarea name="items[${index}][item_notes]" 
                                  class="form-control form-control-sm" 
                                  rows="2" placeholder="Item notes"></textarea>
                    </div>
                    <div>
                        <textarea name="items[${index}][rejection_reason]" 
                                  class="form-control form-control-sm" 
                                  rows="2" placeholder="Rejection reason (if any)"></textarea>
                    </div>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Initialize if PO is pre-selected
@if($purchaseOrder)
    document.addEventListener('DOMContentLoaded', function() {
        loadPurchaseOrder();
    });
@endif

// Ensure form submission is not blocked and log submit event
document.addEventListener('DOMContentLoaded', function() {
    var grvForm = document.getElementById('createGrvForm');
    if (grvForm) {
        grvForm.addEventListener('submit', function(e) {
            console.log('GRV form submit event fired');
            // Remove any event.preventDefault() to allow normal submission
        });
    }
});
</script>
@endsection