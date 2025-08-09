@extends('layouts.app')

@section('title', 'Add New Tenant')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add New Tenant</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.tenants') }}" class="text-decoration-none">Tenants</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Add New</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('landlord.tenants') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tenants
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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tenant Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('landlord.tenants.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tenant Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="domain">Custom Domain (Optional)</label>
                                    <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                           id="domain" name="domain" value="{{ old('domain') }}"
                                           placeholder="e.g., company-name">
                                    <small class="form-text text-muted">Leave blank for auto-generated domain. Will be: [your-input].workflow-management.test</small>
                                    @error('domain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_name">Owner Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('owner_name') is-invalid @enderror" 
                                           id="owner_name" name="owner_name" value="{{ old('owner_name') }}" required>
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_email">Owner Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('owner_email') is-invalid @enderror" 
                                           id="owner_email" name="owner_email" value="{{ old('owner_email') }}" required>
                                    @error('owner_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_phone">Owner Phone</label>
                                    <input type="text" class="form-control @error('owner_phone') is-invalid @enderror" 
                                           id="owner_phone" name="owner_phone" value="{{ old('owner_phone') }}">
                                    @error('owner_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_password">Owner Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('owner_password') is-invalid @enderror" 
                                           id="owner_password" name="owner_password" required minlength="8">
                                    <small class="form-text text-muted">Password for the tenant owner account (min 8 characters)</small>
                                    @error('owner_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city') }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="country">Country</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                                   id="country" name="country" value="{{ old('country', 'South Africa') }}">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subscription_plan">Subscription Package <span class="text-danger">*</span></label>
                                    <select class="form-control @error('subscription_plan') is-invalid @enderror" 
                                            id="subscription_plan" name="subscription_plan" required>
                                        <option value="">Select Package</option>
                                        @foreach($packages as $package)
                                            <option value="{{ $package->name }}" 
                                                    data-price="{{ $package->monthly_price }}"
                                                    data-yearly-price="{{ $package->yearly_price }}"
                                                    data-max-users="{{ $package->max_users }}"
                                                    data-storage="{{ $package->storage_limit_mb }}"
                                                    data-features="{{ json_encode($package->features) }}"
                                                    data-description="{{ $package->description }}"
                                                    {{ old('subscription_plan') === $package->name ? 'selected' : '' }}>
                                                {{ $package->name }} - R{{ number_format($package->monthly_price, 2) }}/month
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subscription_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monthly_fee">Monthly Fee</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('monthly_fee') is-invalid @enderror" 
                                               id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee') }}" readonly>
                                    </div>
                                    <small class="form-text text-muted">Auto-filled based on selected package</small>
                                    @error('monthly_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create Tenant
                            </button>
                            <a href="{{ route('landlord.tenants') }}" class="btn btn-secondary">
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
                    <h6 class="m-0 font-weight-bold text-primary">Selected Package Details</h6>
                </div>
                <div class="card-body" id="package-details">
                    <div class="text-center text-muted">
                        <i class="fas fa-cube fa-3x mb-3"></i>
                        <p>Select a package to view details</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Packages</h6>
                </div>
                <div class="card-body">
                    @foreach($packages as $package)
                        <div class="mb-3 p-3 border rounded package-summary" data-package="{{ $package->name }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $package->name }}</h6>
                                    <div class="h6 text-primary mb-1">R{{ number_format($package->monthly_price, 2) }}/month</div>
                                    @if($package->yearly_price)
                                        <small class="text-success">R{{ number_format($package->yearly_price, 2) }}/year</small>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <small class="text-muted d-block">{{ $package->max_users }} users</small>
                                    <small class="text-muted">{{ number_format($package->storage_limit_mb / 1024, 1) }}GB</small>
                                </div>
                            </div>
                            @if($package->description)
                                <small class="text-muted d-block mt-2">{{ $package->description }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Important Notes</h6>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li>Tenant ID will be auto-generated as next sequential number (21, 22, 23, etc.)</li>
                        <li>If domain is not specified, it will be auto-generated from tenant ID</li>
                        <li>Owner will be created as super admin (level 5) in the tenant system</li>
                        <li>Owner will receive login credentials to access their tenant</li>
                        <li>Monthly fee is automatically set based on selected package</li>
                        <li>Package limits (users, storage) will be enforced for the tenant</li>
                        <li>Each tenant gets a completely fresh database with no existing data</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle subscription plan change to auto-fill monthly fee and show package details
document.getElementById('subscription_plan').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    const yearlyPrice = selectedOption.getAttribute('data-yearly-price');
    const maxUsers = selectedOption.getAttribute('data-max-users');
    const storage = selectedOption.getAttribute('data-storage');
    const features = selectedOption.getAttribute('data-features');
    const description = selectedOption.getAttribute('data-description');
    const monthlyFeeInput = document.getElementById('monthly_fee');
    const packageDetails = document.getElementById('package-details');
    
    if (price) {
        monthlyFeeInput.value = price;
        
        // Show detailed package information
        let featuresArray = [];
        if (features) {
            try {
                featuresArray = JSON.parse(features);
            } catch (e) {
                featuresArray = [];
            }
        }
        
        let detailsHtml = `
            <div class="text-center mb-3">
                <h5 class="text-primary">${this.options[this.selectedIndex].text.split(' - ')[0]}</h5>
                <div class="h4 text-primary">R${parseFloat(price).toFixed(2)}</div>
                <small class="text-muted">per month</small>
        `;
        
        if (yearlyPrice && parseFloat(yearlyPrice) > 0) {
            detailsHtml += `
                <div class="mt-1">
                    <small class="text-success">R${parseFloat(yearlyPrice).toFixed(2)}/year</small>
                </div>
            `;
        }
        
        detailsHtml += `</div>`;
        
        if (description) {
            detailsHtml += `
                <div class="mb-3">
                    <p class="text-muted small">${description}</p>
                </div>
            `;
        }
        
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block mb-1"><strong>Package Limits:</strong></small>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-users text-primary"></i> ${maxUsers} maximum users</li>
                    <li><i class="fas fa-hdd text-primary"></i> ${(parseFloat(storage) / 1024).toFixed(1)}GB storage limit</li>
                </ul>
            </div>
        `;
        
        if (featuresArray && featuresArray.length > 0) {
            detailsHtml += `
                <div class="mb-3">
                    <small class="text-muted d-block mb-1"><strong>Included Features:</strong></small>
                    <ul class="list-unstyled small">
            `;
            
            featuresArray.forEach(feature => {
                detailsHtml += `<li><i class="fas fa-check text-success"></i> ${feature}</li>`;
            });
            
            detailsHtml += `
                    </ul>
                </div>
            `;
        }
        
        packageDetails.innerHTML = detailsHtml;
        
        // Highlight the selected package in the sidebar
        document.querySelectorAll('.package-summary').forEach(summary => {
            summary.style.backgroundColor = '';
            summary.style.borderColor = '';
        });
        
        const selectedPackageName = this.options[this.selectedIndex].value;
        const selectedSummary = document.querySelector(`[data-package="${selectedPackageName}"]`);
        if (selectedSummary) {
            selectedSummary.style.backgroundColor = '#f8f9fc';
            selectedSummary.style.borderColor = '#5a5c69';
        }
        
    } else {
        monthlyFeeInput.value = '';
        packageDetails.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-cube fa-3x mb-3"></i>
                <p>Select a package to view details</p>
            </div>
        `;
        
        // Remove highlighting
        document.querySelectorAll('.package-summary').forEach(summary => {
            summary.style.backgroundColor = '';
            summary.style.borderColor = '';
        });
    }
});

// Allow clicking on package summaries to select them
document.querySelectorAll('.package-summary').forEach(summary => {
    summary.style.cursor = 'pointer';
    summary.addEventListener('click', function() {
        const packageName = this.getAttribute('data-package');
        const selectElement = document.getElementById('subscription_plan');
        selectElement.value = packageName;
        selectElement.dispatchEvent(new Event('change'));
    });
});
</script>
@endsection
