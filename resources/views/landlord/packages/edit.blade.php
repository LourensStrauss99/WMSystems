@extends('layouts.app')

@section('title', 'Edit Package')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Package: {{ $package->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.packages.index') }}" class="text-decoration-none">Packages</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit {{ $package->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('landlord.packages.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Packages
            </a>
        </div>
    </div>

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
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Package Details</h6>
                    <div>
                        @if($package->tenants_count > 0)
                            <span class="badge badge-warning">{{ $package->tenants_count }} tenant(s) using this package</span>
                        @else
                            <span class="badge badge-success">No tenants using this package</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('landlord.packages.update', $package->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Package Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $package->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sort_order">Sort Order</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}">
                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $package->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monthly_price">Monthly Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('monthly_price') is-invalid @enderror" 
                                               id="monthly_price" name="monthly_price" value="{{ old('monthly_price', $package->monthly_price) }}" required>
                                    </div>
                                    @if($package->tenants_count > 0)
                                        <small class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Price changes will affect {{ $package->tenants_count }} active tenant(s)
                                        </small>
                                    @endif
                                    @error('monthly_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="yearly_price">Yearly Price (Optional)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('yearly_price') is-invalid @enderror" 
                                               id="yearly_price" name="yearly_price" value="{{ old('yearly_price', $package->yearly_price) }}">
                                    </div>
                                    <small class="form-text text-muted">Leave blank if not offering yearly billing</small>
                                    @error('yearly_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_users">Maximum Users <span class="text-danger">*</span></label>
                                    <input type="number" min="1" class="form-control @error('max_users') is-invalid @enderror" 
                                           id="max_users" name="max_users" value="{{ old('max_users', $package->max_users) }}" required>
                                    @if($package->tenants_count > 0)
                                        <small class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Reducing user limits may affect existing tenants
                                        </small>
                                    @endif
                                    @error('max_users')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="storage_limit_gb">Storage Limit (GB) <span class="text-danger">*</span></label>
                                    <input type="number" min="0.1" step="0.1" class="form-control @error('storage_limit_mb') is-invalid @enderror" 
                                           id="storage_limit_gb" name="storage_limit_gb" value="{{ old('storage_limit_gb', $package->storage_limit_mb / 1024) }}">
                                    <input type="hidden" id="storage_limit_mb" name="storage_limit_mb" value="{{ old('storage_limit_mb', $package->storage_limit_mb) }}">
                                    @if($package->tenants_count > 0)
                                        <small class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Reducing storage may affect existing tenant data
                                        </small>
                                    @endif
                                    @error('storage_limit_mb')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Package Features <span class="text-danger">*</span></label>
                            <div id="features-container">
                                @if(old('features'))
                                    @foreach(old('features') as $index => $feature)
                                        <div class="input-group mb-2 feature-input">
                                            <input type="text" class="form-control" name="features[]" value="{{ $feature }}" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger remove-feature">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($package->features as $feature)
                                        <div class="input-group mb-2 feature-input">
                                            <input type="text" class="form-control" name="features[]" value="{{ $feature }}" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger remove-feature">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                                <i class="fas fa-plus"></i> Add Feature
                            </button>
                            @if($package->tenants_count > 0)
                                <div class="mt-1">
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Feature changes will affect {{ $package->tenants_count }} active tenant(s)
                                    </small>
                                </div>
                            @endif
                            @error('features')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Package is Active
                                </label>
                                @if($package->tenants_count > 0 && $package->is_active)
                                    <div>
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Deactivating this package will prevent new tenant registrations but won't affect existing tenants
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Package
                            </button>
                            <a href="{{ route('landlord.packages.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Package Preview</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h4 text-primary" id="preview-price">R{{ number_format($package->monthly_price, 2) }}</div>
                        <small class="text-muted">per month</small>
                        @if($package->yearly_price)
                        <div class="mt-1" id="preview-yearly">
                            <small class="text-success" id="preview-yearly-price">R{{ number_format($package->yearly_price, 2) }}/year</small>
                        </div>
                        @else
                        <div class="mt-1" id="preview-yearly" style="display: none;">
                            <small class="text-success" id="preview-yearly-price">R0.00/year</small>
                        </div>
                        @endif
                    </div>

                    @if($package->description)
                    <div class="mb-3" id="preview-description">
                        <p class="text-muted" id="preview-description-text">{{ $package->description }}</p>
                    </div>
                    @else
                    <div class="mb-3" id="preview-description" style="display: none;">
                        <p class="text-muted" id="preview-description-text"></p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1"><strong>Limits:</strong></small>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-users text-primary"></i> <span id="preview-users">{{ $package->max_users }}</span> users</li>
                            <li><i class="fas fa-hdd text-primary"></i> <span id="preview-storage">{{ number_format($package->storage_limit_mb / 1024, 1) }}</span>GB storage</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1"><strong>Features:</strong></small>
                        <ul class="list-unstyled small" id="preview-features">
                            @foreach($package->features as $feature)
                                <li><i class="fas fa-check text-success"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            @if($package->tenants_count > 0)
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Package Usage</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small font-weight-bold">Active Tenants</span>
                            <span class="small">{{ $package->tenants_count }}</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;"></div>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle"></i> 
                        This package is currently being used by {{ $package->tenants_count }} tenant(s). 
                        Changes to limits or features will affect these tenants.
                    </p>
                </div>
            </div>
            @endif

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Common Features</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1"><strong>Basic Features:</strong></small>
                        <div class="d-flex flex-wrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm m-1 add-common-feature" data-feature="Customer Management">Customer Management</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm m-1 add-common-feature" data-feature="Order Tracking">Order Tracking</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm m-1 add-common-feature" data-feature="Basic Reports">Basic Reports</button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1"><strong>Advanced Features:</strong></small>
                        <div class="d-flex flex-wrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm m-1 add-common-feature" data-feature="Advanced Analytics">Advanced Analytics</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm m-1 add-common-feature" data-feature="API Access">API Access</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm m-1 add-common-feature" data-feature="Priority Support">Priority Support</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm m-1 add-common-feature" data-feature="Custom Branding">Custom Branding</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add feature functionality
    document.getElementById('add-feature').addEventListener('click', function() {
        const container = document.getElementById('features-container');
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mb-2 feature-input';
        newFeature.innerHTML = `
            <input type="text" class="form-control" name="features[]" placeholder="Enter a feature" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newFeature);
        updatePreview();
    });

    // Remove feature functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            const featureInputs = document.querySelectorAll('.feature-input');
            if (featureInputs.length > 1) {
                e.target.closest('.feature-input').remove();
                updatePreview();
            }
        }
    });

    // Add common features
    document.querySelectorAll('.add-common-feature').forEach(button => {
        button.addEventListener('click', function() {
            const feature = this.getAttribute('data-feature');
            const container = document.getElementById('features-container');
            const newFeature = document.createElement('div');
            newFeature.className = 'input-group mb-2 feature-input';
            newFeature.innerHTML = `
                <input type="text" class="form-control" name="features[]" value="${feature}" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-feature">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(newFeature);
            updatePreview();
        });
    });

    // Storage GB to MB conversion
    document.getElementById('storage_limit_gb').addEventListener('input', function() {
        const gb = parseFloat(this.value) || 0;
        document.getElementById('storage_limit_mb').value = Math.round(gb * 1024);
        updatePreview();
    });

    // Update preview on input changes
    ['monthly_price', 'yearly_price', 'description', 'max_users'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    document.addEventListener('input', function(e) {
        if (e.target.name === 'features[]') {
            updatePreview();
        }
    });

    function updatePreview() {
        // Update price
        const monthlyPrice = parseFloat(document.getElementById('monthly_price').value) || 0;
        document.getElementById('preview-price').textContent = 'R' + monthlyPrice.toFixed(2);

        // Update yearly price
        const yearlyPrice = parseFloat(document.getElementById('yearly_price').value) || 0;
        if (yearlyPrice > 0) {
            document.getElementById('preview-yearly').style.display = 'block';
            document.getElementById('preview-yearly-price').textContent = 'R' + yearlyPrice.toFixed(2) + '/year';
        } else {
            document.getElementById('preview-yearly').style.display = 'none';
        }

        // Update description
        const description = document.getElementById('description').value;
        if (description) {
            document.getElementById('preview-description').style.display = 'block';
            document.getElementById('preview-description-text').textContent = description;
        } else {
            document.getElementById('preview-description').style.display = 'none';
        }

        // Update users
        const maxUsers = document.getElementById('max_users').value || 5;
        document.getElementById('preview-users').textContent = maxUsers;

        // Update storage
        const storageGb = parseFloat(document.getElementById('storage_limit_gb').value) || 1;
        document.getElementById('preview-storage').textContent = storageGb.toFixed(1);

        // Update features
        const features = document.querySelectorAll('input[name="features[]"]');
        const featuresList = document.getElementById('preview-features');
        featuresList.innerHTML = '';
        
        features.forEach(input => {
            if (input.value.trim()) {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fas fa-check text-success"></i> ${input.value.trim()}`;
                featuresList.appendChild(li);
            }
        });

        if (featuresList.children.length === 0) {
            featuresList.innerHTML = '<li><i class="fas fa-check text-success"></i> Add features above</li>';
        }
    }

    // Initial preview update
    updatePreview();
});
</script>
@endsection
