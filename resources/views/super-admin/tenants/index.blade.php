@extends('layouts.app')

@section('title', 'Tenant Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Tenant Management</h1>
                    <p class="text-muted mb-0">Manage all registered companies</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('tenant.show-registration') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Add New Tenant
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($tenants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Owner</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Subscription</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenants as $tenant)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $tenant->name }}</div>
                                                <small class="text-muted">DB: {{ $tenant->database_name }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $tenant->owner_name }}</div>
                                                <small class="text-muted">{{ $tenant->owner_email }}</small>
                                            </td>
                                            <td>
                                                @if($tenant->owner_phone)
                                                    <div>{{ $tenant->owner_phone }}</div>
                                                @endif
                                                @if($tenant->address)
                                                    <small class="text-muted">{{ Str::limit($tenant->address, 30) }}</small>
                                                @endif
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
                                                        @if($tenant->subscription_expires_at->isPast())
                                                            <span class="text-danger">Expired {{ $tenant->subscription_expires_at->diffForHumans() }}</span>
                                                        @else
                                                            Expires {{ $tenant->subscription_expires_at->diffForHumans() }}
                                                        @endif
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
                                                       class="btn btn-outline-primary" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('super-admin.login-as-tenant', $tenant) }}" 
                                                       class="btn btn-outline-success" 
                                                       title="Login as Tenant">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                    </a>
                                                    @if($tenant->status === 'active')
                                                        <form method="POST" action="{{ route('super-admin.tenants.suspend', $tenant) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-warning" 
                                                                    title="Suspend Tenant"
                                                                    onclick="return confirm('Are you sure you want to suspend this tenant?')">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('super-admin.tenants.activate', $tenant) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-success" 
                                                                    title="Activate Tenant">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('super-admin.tenants.delete', $tenant) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger" 
                                                                title="Delete Tenant"
                                                                onclick="return confirm('Are you sure you want to permanently delete this tenant and their database? This action cannot be undone!')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $tenants->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No tenants found</h4>
                            <p class="text-muted">No companies have registered yet.</p>
                            <a href="{{ route('tenant.show-registration') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add First Tenant
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
