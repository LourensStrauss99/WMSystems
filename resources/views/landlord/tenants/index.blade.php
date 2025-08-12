@extends('layouts.app')

@section('title', 'Tenant Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Tenant Management</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <button class="btn btn-success" onclick="window.location.href='{{ route('landlord.tenants.create') }}'">
                        <i class="fas fa-plus"></i> Add New Tenant
                    </button>
                    
                    <!-- TEMPORARY TEST FORM - Remove after testing -->
                    <form method="POST" action="{{ route('landlord.tenants.store') }}" style="display: inline-block; margin-left: 10px;" onsubmit="alert('Test form submitting!');">
                        @csrf
                        <input type="hidden" name="name" value="Test Company">
                        <input type="hidden" name="tenant_id" value="test123">
                        <input type="hidden" name="owner_name" value="Test Owner">
                        <input type="hidden" name="owner_email" value="test@example.com">
                        <input type="hidden" name="owner_password" value="password123">
                        <input type="hidden" name="subscription_plan" value="basic">
                        <input type="hidden" name="monthly_fee" value="4500">
                        <button type="submit" class="btn btn-warning">TEST FORM</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('landlord.tenants.index') }}" class="row align-items-end">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Tenant name, email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="package" class="form-label">Package</label>
                            <select name="package" id="package" class="form-control">
                                <option value="">All Packages</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->slug }}" {{ request('package') === $pkg->slug ? 'selected' : '' }}>
                                        {{ $pkg->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('landlord.tenants.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Tenants ({{ $tenants->total() }} total)
                    </h6>
                </div>
                <div class="card-body">
                    @if($tenants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Owner</th>
                                        <th>Package</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Last Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenants as $tenant)
                                        @php
                                            $latestPayment = $tenant->landlordPayments->where('status', 'completed')->first();
                                            $hasOverdueInvoices = $tenant->landlordInvoices->where('status', 'pending')->where('due_date', '<', now())->count() > 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $tenant->name }}</strong>
                                                    @if($tenant->domains->count() > 0)
                                                        <br>
                                                        <a href="http://{{ $tenant->domains->first()->domain }}" target="_blank" class="text-primary text-decoration-underline">
                                                            {{ $tenant->domains->first()->domain }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $tenant->owner_name }}
                                                    <br><small class="text-muted">{{ $tenant->owner_email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ ucfirst(str_replace('-', ' ', $tenant->subscription_plan ?? 'None')) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($tenant->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                                
                                                @if($hasOverdueInvoices)
                                                    <br><span class="badge badge-warning">Overdue</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $tenant->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                @if($latestPayment)
                                                    <div>
                                                        <strong>{{ $latestPayment->currency }} {{ number_format($latestPayment->amount, 2) }}</strong>
                                                        <br><small class="text-muted">{{ $latestPayment->payment_date->format('M d, Y') }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No payments</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('landlord.tenants.show', $tenant) }}" class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('landlord.tenants.edit', $tenant) }}" class="btn btn-outline-success" title="Edit Tenant">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#" onclick="generateInvoice({{ $tenant->id }})">
                                                                <i class="fas fa-file-invoice"></i> Generate Invoice
                                                            </a>
                                                            <a class="dropdown-item" href="#" onclick="generateStatement({{ $tenant->id }})">
                                                                <i class="fas fa-file-alt"></i> Account Statement
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            @if($tenant->is_active)
                                                                <a class="dropdown-item text-warning" href="#" onclick="suspendTenant({{ $tenant->id }})">
                                                                    <i class="fas fa-pause"></i> Suspend
                                                                </a>
                                                            @else
                                                                <a class="dropdown-item text-success" href="#" onclick="activateTenant({{ $tenant->id }})">
                                                                    <i class="fas fa-play"></i> Activate
                                                                </a>
                                                            @endif
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this tenant?')) document.getElementById('delete-tenant-form-{{ $tenant->id }}').submit();">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <form id="delete-tenant-form-{{ $tenant->id }}" action="{{ route('landlord.tenants.destroy', $tenant) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $tenants->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No tenants found matching your criteria.</p>
                            <button class="btn btn-primary" onclick="window.location.href='{{ route('landlord.tenants.create') }}'">
                                <i class="fas fa-plus"></i> Add Your First Tenant
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Tenant Modal -->
<div class="modal fade" id="createTenantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Tenant</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="createTenantForm" method="POST" action="{{ route('landlord.tenants.store') }}" onsubmit="console.log('Form onsubmit triggered'); this.querySelector('button[type=submit]').innerHTML='Creating...'; this.querySelector('button[type=submit]').disabled=true;">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tenant_name">Tenant Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tenant_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tenant_id">Tenant ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tenant_id" name="tenant_id" required
                                       placeholder="e.g., company-id">
                                <small class="form-text text-muted">Unique identifier for the tenant</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="domain">Domain (optional)</label>
                                <input type="text" class="form-control" id="domain" name="domain"
                                       placeholder="e.g., company-name">
                                <small class="form-text text-muted">Leave blank to auto-generate: tenant-id.workflow-management.test</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner_name">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner_email">Owner Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="owner_email" name="owner_email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subscription_plan">Subscription Package <span class="text-danger">*</span></label>
                                <select class="form-control" id="subscription_plan" name="subscription_plan" required>
                                    <option value="">Select Package</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->name }}" data-price="{{ $package->price }}">
                                            {{ ucfirst($package->name) }} - R{{ number_format($package->price, 2) }}/month
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monthly_fee">Monthly Fee</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">R</span>
                                    </div>
                                    <input type="number" step="0.01" class="form-control" id="monthly_fee" name="monthly_fee" readonly>
                                </div>
                                <small class="form-text text-muted">Auto-filled based on selected package</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner_password">Owner Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="owner_password" name="owner_password" required minlength="8">
                                <small class="form-text text-muted">Password for the tenant owner account (min 8 characters)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner_phone">Owner Phone</label>
                                <input type="text" class="form-control" id="owner_phone" name="owner_phone">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
console.log('JavaScript is loading...');

function viewTenant(tenantId) {
    // Implement view tenant details
    alert('View tenant details for ID: ' + tenantId);
}

function editTenant(tenantId) {
    // Implement edit tenant
    alert('Edit tenant for ID: ' + tenantId);
}

function generateInvoice(tenantId) {
    // Implement invoice generation
    alert('Generate invoice for tenant ID: ' + tenantId);
}

function generateStatement(tenantId) {
    // Implement statement generation
    alert('Generate statement for tenant ID: ' + tenantId);
}

function suspendTenant(tenantId) {
    if (confirm('Are you sure you want to suspend this tenant? They will lose access to their account.')) {
        // Implement suspend functionality
        alert('Suspend tenant ID: ' + tenantId);
    }
}

function activateTenant(tenantId) {
    if (confirm('Are you sure you want to activate this tenant?')) {
        // Implement activate functionality
        alert('Activate tenant ID: ' + tenantId);
    }
}

function deleteTenant(tenantId) {
    if (confirm('⚠️ WARNING: This will permanently delete the tenant and ALL their data. This action cannot be undone.\n\nAre you absolutely sure?')) {
        // Implement delete functionality with proper data handling
        alert('Delete tenant ID: ' + tenantId);
    }
}

// Simple form validation and submission

console.log('JavaScript loaded successfully');

// Add Bootstrap JS and jQuery for dropdown functionality
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

// Show modal if there are validation errors
@if ($errors->any())
    $(document).ready(function() {
        $('#createTenantModal').modal('show');
    });
@endif
</script>
@endsection
