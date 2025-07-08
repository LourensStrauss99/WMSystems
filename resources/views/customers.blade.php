{{-- filepath: resources/views/customers.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="text-dark fw-bold">
                <i class="fas fa-users me-2 text-primary"></i>
                Customer Directory
            </h2>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                @if(request('search'))
                    <a href="{{ route('customers.index') }}" 
                       class="btn btn-outline-secondary"
                       title="Back to All Customers">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back
                    </a>
                @endif
                <a href="{{ route('client.create') }}" 
                   class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>
                    Add New Customer 
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Search Customers
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ old('search', $search ?? '') }}" 
                               placeholder="Search by name, surname, phone, or email..."
                               class="form-control" />
                        @if(request('search'))
                            <a href="{{ route('customers.index') }}" 
                               class="btn btn-outline-secondary"
                               title="Clear Search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label for="perPage" class="form-label">
                        <i class="fas fa-list me-1"></i>Show Entries
                    </label>
                    <select name="perPage" id="perPage" class="form-select" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Search
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-1"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="btn-group mb-3" role="group">
        <a href="{{ route('customers.index') }}" 
           class="btn btn-outline-primary {{ !request('filter') ? 'active' : '' }}">
            All Customers
        </a>
        <a href="{{ route('customers.index', ['filter' => 'active']) }}" 
           class="btn btn-outline-success {{ request('filter') == 'active' ? 'active' : '' }}">
            Active Only
        </a>
        <a href="{{ route('customers.index', ['filter' => 'inactive']) }}" 
           class="btn btn-outline-warning {{ request('filter') == 'inactive' ? 'active' : '' }}">
            Inactive Only
        </a>
    </div>

    <!-- Search Results Summary -->
    @if(request('search'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Search Results:</strong> 
            Found {{ $customers->total() }} customer(s) matching "{{ request('search') }}"
            @if($customers->total() == 0)
                - <a href="{{ route('customers.index') }}" class="alert-link">Show all customers</a>
            @endif
        </div>
    @endif

    <!-- Customers Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>
                        Customer List
                    </h5>
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-dark">
                        {{ $customers->total() }} Total
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" class="text-center" style="width: 60px;">
                                    <i class="fas fa-hashtag"></i>
                                </th>
                                <th scope="col">
                                    <i class="fas fa-user me-1"></i>Name
                                </th>
                                <th scope="col">
                                    <i class="fas fa-user-tag me-1"></i>Surname
                                </th>
                                <th scope="col">
                                    <i class="fas fa-phone me-1"></i>Telephone
                                </th>
                                <th scope="col">
                                    <i class="fas fa-map-marker-alt me-1"></i>Address
                                </th>
                                <th scope="col">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </th>
                                <th>Status</th>
                                <th scope="col" class="text-center" style="width: 120px;">
                                    <i class="fas fa-cog"></i> Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr class="align-middle">
                                    <td class="text-center text-muted fw-bold">
                                        {{ $customer->id }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($customer->name ?? 'N', 0, 1)) }}
                                            </div>
                                            <strong>{{ $customer->name ?? 'N/A' }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $customer->surname ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($customer->telephone)
                                            <a href="tel:{{ $customer->telephone }}" 
                                               class="text-decoration-none text-success">
                                                <i class="fas fa-phone me-1"></i>
                                                {{ $customer->telephone }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($customer->address)
                                            <div class="text-truncate" style="max-width: 200px;" 
                                                 title="{{ $customer->address }}">
                                                <i class="fas fa-map-pin me-1 text-info"></i>
                                                {{ $customer->address }}
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($customer->email)
                                            <a href="mailto:{{ $customer->email }}" 
                                               class="text-decoration-none text-primary">
                                                <i class="fas fa-envelope me-1"></i>
                                                <span class="text-truncate d-inline-block" 
                                                      style="max-width: 150px;">
                                                    {{ $customer->email }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{!! $customer->status_badge !!}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('client.show', $customer->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Customer Details"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye me-1"></i>
                                                <span class="d-none d-md-inline">View</span>
                                            </a>
                                            <a href="{{ route('client.edit', $customer->id) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit Customer"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit me-1"></i>
                                                <span class="d-none d-md-inline">Edit</span>
                                            </a>
                                            <!-- Debug: {{ route('customers.destroy', $customer) }} -->
                                            <form action="{{ route('customers.toggle-status', $customer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-outline-{{ $customer->is_active ? 'warning' : 'success' }} btn-sm"
                                                        title="{{ $customer->is_active ? 'Mark as Inactive' : 'Mark as Active' }}"
                                                        onclick="return confirm('{{ $customer->is_active ? 'Mark this customer as inactive?' : 'Reactivate this customer?' }}')">
                                                    <i class="fas fa-{{ $customer->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-muted"></i>
                    </div>
                    <h4 class="text-muted">No Customers Found</h4>
                    @if(request('search'))
                        <p class="text-muted">
                            No customers match your search criteria "{{ request('search') }}"
                        </p>
                        <a href="{{ route('customers.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i>Show All Customers
                        </a>
                    @else
                        <p class="text-muted">
                            No customers have been added yet.
                        </p>
                        <a href="{{ route('client.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Add First Customer
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination Section -->
    @if($customers->hasPages())
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} 
                    of {{ $customers->total() }} entries
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Custom CSS for styling -->
<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.8rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.9rem;
}

.table td {
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.btn-group .btn {
    margin-right: 2px;
    transition: all 0.3s ease;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Enhanced button styles */
.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.alert {
    border: none;
    border-radius: 8px;
}

.input-group .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Icon styling */
.btn i {
    font-size: 0.9rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .table th, .table td {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    
    .btn-group .btn span {
        display: none !important;
    }
    
    .avatar-circle {
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
    }
    
    .btn i {
        font-size: 0.8rem;
        margin-right: 0 !important;
    }
}

/* Tooltip styling */
.tooltip {
    font-size: 0.8rem;
}
</style>
@endsection