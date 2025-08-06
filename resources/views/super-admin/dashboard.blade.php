@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Super Admin Dashboard</h1>
                    <p class="text-muted mb-0">Multi-Tenant Management System</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.tenants') }}" class="btn btn-outline-primary">
                        <i class="fas fa-building me-1"></i>Manage Tenants
                    </a>
                    <a href="{{ route('tenant.show-registration') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Add New Tenant
                    </a>
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
                                        <i class="fas fa-building fa-2x text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Total Tenants</h6>
                                    <h3 class="mb-0">{{ $stats['total_tenants'] }}</h3>
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
                                        <i class="fas fa-check-circle fa-2x text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Active Tenants</h6>
                                    <h3 class="mb-0">{{ $stats['active_tenants'] }}</h3>
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
                                        <i class="fas fa-gift fa-2x text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Trial Tenants</h6>
                                    <h3 class="mb-0">{{ $stats['trial_tenants'] }}</h3>
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
                                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-0">Expired Trials</h6>
                                    <h3 class="mb-0">{{ $stats['expired_trials'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tenants -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock me-2"></i>Recent Tenant Registrations
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($recent_tenants->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Owner</th>
                                                <th>Status</th>
                                                <th>Subscription</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_tenants as $tenant)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold">{{ $tenant->name }}</div>
                                                        <small class="text-muted">{{ $tenant->database_name }}</small>
                                                    </td>
                                                    <td>
                                                        <div>{{ $tenant->owner_name }}</div>
                                                        <small class="text-muted">{{ $tenant->owner_email }}</small>
                                                    </td>
                                                    <td>
                                                        @if($tenant->status === 'active')
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($tenant->status === 'suspended')
                                                            <span class="badge bg-warning">Suspended</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div>{{ ucfirst($tenant->subscription_plan) }}</div>
                                                        @if($tenant->subscription_expires_at)
                                                            <small class="text-muted">
                                                                Expires: {{ $tenant->subscription_expires_at->format('M d, Y') }}
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div>{{ $tenant->created_at->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ $tenant->created_at->diffForHumans() }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" 
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('super-admin.login-as-tenant', $tenant) }}" 
                                                               class="btn btn-outline-success">
                                                                <i class="fas fa-sign-in-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No tenants registered yet</h5>
                                    <p class="text-muted">Tenants will appear here when they register</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
