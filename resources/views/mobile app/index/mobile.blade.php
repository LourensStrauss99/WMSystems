@extends('layouts.mobile')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold text-primary"><i class="fas fa-clipboard-list me-2"></i>Mobile Jobcard Index</h4>
    <div class="row g-3">
        <!-- Example jobcard listing, replace with dynamic data -->
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-2">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between py-3 px-3">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fas fa-clipboard me-2 text-primary"></i>Jobcard <div id="JC-20250715-0003"></div></h5>
                        <div class="small text-muted">Client: Deon</div>
                        <div class="small text-muted">Status: <span class="badge bg-warning text-dark">Assigned</span></div>
                    </div>
                    <div class="mt-3 mt-md-0 d-flex gap-2">
<a href="{{ route('jobcard.edit.mobile', ['id' => 1]) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Edit</a>
                        <a href="#" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add more jobcards here -->
    </div>
    <div class="text-center mt-4">
        <button class="btn btn-success"><i class="fas fa-sync-alt me-1"></i>Refresh</button>
    </div>
</div>
@endsection
