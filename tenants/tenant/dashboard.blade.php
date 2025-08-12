@extends('layouts.app')

@section('title', 'Company Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $tenant->name }}</h1>
                    <p class="text-muted mb-0">Company Dashboard</p>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->canAccessCompanySettings())
                        <a href="{{ route('tenant.settings') }}" class="btn btn-outline-primary">
                            <i class="fas fa-cog me-1"></i>Settings
                        </a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-plus me-1"></i>Quick Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('jobcard.index') }}">
                                <i class="fas fa-clipboard-list me-2"></i>Manage Jobcards
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('customers.index') }}">
                                <i class="fas fa-user-plus me-2"></i>Manage Customers
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Company Status Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-1">
                                        <i class="fas fa-shield-alt text-success me-2"></i>
                                        Account Status: {{ ucfirst($tenant->status) }}
                                    </h5>
                                    <p class="text-muted mb-0">
                                        @if($tenant->subscription_plan === 'trial')
                                            <span class="badge bg-info">Free Trial</span>
                                            - {{ $tenant->subscription_expires_at->diffForHumans() }}
                                        @elseif($tenant->subscription_plan === 'active')
                                            <span class="badge bg-success">Active Subscription</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($tenant->subscription_plan) }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <small class="text-muted">
                                        Database: <code>{{ $tenant->database_name }}</code>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Total Jobcards</h6>
                                    <h3 class="mb-0">{{ $tenant->jobcards_count ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-users fa-2x text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Customers</h6>
                                    <h3 class="mb-0">{{ $tenant->customers_count ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-clock fa-2x text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Pending Jobs</h6>
                                    <h3 class="mb-0">{{ $tenant->pending_jobs_count ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-user-tie fa-2x text-info"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Team Members</h6>
                                    <h3 class="mb-0">{{ $tenant->users_count ?? 1 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tachometer-alt me-2"></i>Quick Navigation
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ route('jobcard.index') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="fas fa-clipboard-list fa-2x d-block mb-2"></i>
                                        <strong>Manage Jobcards</strong>
                                        <div class="small text-muted">Create and track work orders</div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('customers.index') }}" class="btn btn-outline-success w-100 py-3">
                                        <i class="fas fa-users fa-2x d-block mb-2"></i>
                                        <strong>Customer Management</strong>
                                        <div class="small text-muted">Add and manage customers</div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-warning w-100 py-3">
                                        <i class="fas fa-boxes fa-2x d-block mb-2"></i>
                                        <strong>Inventory</strong>
                                        <div class="small text-muted">Track parts and supplies</div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('reports.index') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                        <strong>Reports</strong>
                                        <div class="small text-muted">View business analytics</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Company Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Company Name</label>
                                <div class="fw-bold">{{ $tenant->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Owner</label>
                                <div>{{ $tenant->owner_name }}</div>
                            </div>
                            @if($tenant->owner_phone)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Phone</label>
                                <div>{{ $tenant->owner_phone }}</div>
                            </div>
                            @endif
                            @if($tenant->address)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Address</label>
                                <div class="small">{{ $tenant->address }}</div>
                            </div>
                            @endif
                            <div class="mb-0">
                                <label class="form-label text-muted small">Registered</label>
                                <div class="small">{{ $tenant->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
