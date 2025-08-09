@extends('layouts.app')

@section('title', 'Subscription Packages')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Subscription Packages</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Packages</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('landlord.packages.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Package
            </a>
            <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Packages Grid -->
    <div class="row">
        @forelse($packages as $package)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100 {{ !$package->is_active ? 'border-secondary' : '' }}">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">{{ $package->name }}</h6>
                        <div>
                            @if($package->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="text-center mb-3">
                            <div class="h2 text-primary">R{{ number_format($package->monthly_price, 2) }}</div>
                            <small class="text-muted">per month</small>
                            @if($package->yearly_price)
                                <div class="mt-1">
                                    <small class="text-success">R{{ number_format($package->yearly_price, 2) }}/year</small>
                                </div>
                            @endif
                        </div>

                        @if($package->description)
                            <p class="text-muted">{{ $package->description }}</p>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><strong>Limits:</strong></small>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-users text-primary"></i> {{ $package->max_users }} users</li>
                                <li><i class="fas fa-hdd text-primary"></i> {{ number_format($package->storage_limit_mb / 1024, 1) }}GB storage</li>
                            </ul>
                        </div>

                        @if($package->features && count($package->features) > 0)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1"><strong>Features:</strong></small>
                                <ul class="list-unstyled small">
                                    @foreach($package->features as $feature)
                                        <li><i class="fas fa-check text-success"></i> {{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Sort Order: {{ $package->sort_order }}</small>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('landlord.packages.edit', $package) }}" class="btn btn-outline-primary" title="Edit Package">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete Package" 
                                            onclick="deletePackage({{ $package->id }}, '{{ $package->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-muted">No packages found</h5>
                        <p class="text-muted">Create your first subscription package to get started.</p>
                        <a href="{{ route('landlord.packages.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Package
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Summary Statistics -->
    @if($packages->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Package Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-primary">{{ $packages->count() }}</div>
                                    <small class="text-muted">Total Packages</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-success">{{ $packages->where('is_active', true)->count() }}</div>
                                    <small class="text-muted">Active Packages</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-info">R{{ number_format($packages->min('monthly_price'), 2) }}</div>
                                    <small class="text-muted">Lowest Price</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-warning">R{{ number_format($packages->max('monthly_price'), 2) }}</div>
                                    <small class="text-muted">Highest Price</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the package "<span id="packageName"></span>"?</p>
                <p class="text-warning small">This action cannot be undone. The package will only be deleted if no tenants are currently using it.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Package</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deletePackage(packageId, packageName) {
    document.getElementById('packageName').textContent = packageName;
    document.getElementById('deleteForm').action = `/landlord/packages/${packageId}`;
    $('#deleteModal').modal('show');
}
</script>
@endsection
