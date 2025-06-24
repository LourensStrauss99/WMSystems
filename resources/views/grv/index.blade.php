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
            <a href="{{ route('master.settings') }}" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Settings
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-truck-loading fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">GRV System Coming Soon</h5>
            <p class="text-muted">This feature is under development. You'll be able to manage goods received vouchers here.</p>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <i class="fas fa-clipboard-check fa-2x text-primary mb-2"></i>
                            <h6>Receive Goods</h6>
                            <small class="text-muted">Process incoming deliveries</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <i class="fas fa-check-double fa-2x text-success mb-2"></i>
                            <h6>Quality Control</h6>
                            <small class="text-muted">Verify received items</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <i class="fas fa-warehouse fa-2x text-info mb-2"></i>
                            <h6>Update Inventory</h6>
                            <small class="text-muted">Automatic stock updates</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection