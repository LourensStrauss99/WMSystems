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

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-tools fa-3x text-warning mb-3"></i>
            <h5 class="text-warning">Feature Under Development</h5>
            <p class="text-muted">The GRV creation form is currently being developed.</p>
            <a href="{{ route('grv.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Return to GRV List
            </a>
        </div>
    </div>
</div>
@endsection