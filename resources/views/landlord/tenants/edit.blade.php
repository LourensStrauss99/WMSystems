@extends('layouts.app')

@section('title', 'Edit Tenant')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Tenant: {{ $tenant->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.tenants') }}" class="text-decoration-none">Tenants</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.tenants.show', $tenant) }}" class="text-decoration-none">{{ $tenant->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('landlord.tenants.show', $tenant) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Details
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tenant Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('landlord.tenants.update', $tenant) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_name">Owner Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('owner_name') is-invalid @enderror" 
                                           id="owner_name" name="owner_name" value="{{ old('owner_name', $tenant->owner_name) }}" required>
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_email">Owner Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('owner_email') is-invalid @enderror" 
                                           id="owner_email" name="owner_email" value="{{ old('owner_email', $tenant->owner_email) }}" required>
                                    @error('owner_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_phone">Owner Phone</label>
                                    <input type="text" class="form-control @error('owner_phone') is-invalid @enderror" 
                                           id="owner_phone" name="owner_phone" value="{{ old('owner_phone', $tenant->owner_phone) }}">
                                    @error('owner_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address', $tenant->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $tenant->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                           id="country" name="country" value="{{ old('country', $tenant->country) }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subscription_plan">Subscription Plan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('subscription_plan') is-invalid @enderror" 
                                            id="subscription_plan" name="subscription_plan" required>
                                        @foreach($packages as $package)
                                            <option value="{{ $package->name }}" 
                                                    {{ old('subscription_plan', $tenant->subscription_plan) === $package->name ? 'selected' : '' }}>
                                                {{ ucfirst($package->name) }} - R{{ number_format($package->price, 2) }}/month
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
                                    <label for="monthly_fee">Monthly Fee <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('monthly_fee') is-invalid @enderror" 
                                               id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', $tenant->monthly_fee) }}" required>
                                    </div>
                                    @error('monthly_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('payment_status') is-invalid @enderror" 
                                            id="payment_status" name="payment_status" required>
                                        <option value="active" {{ old('payment_status', $tenant->payment_status) === 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="suspended" {{ old('payment_status', $tenant->payment_status) === 'suspended' ? 'selected' : '' }}>
                                            Suspended
                                        </option>
                                        <option value="cancelled" {{ old('payment_status', $tenant->payment_status) === 'cancelled' ? 'selected' : '' }}>
                                            Cancelled
                                        </option>
                                    </select>
                                    @error('payment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="next_payment_due">Next Payment Due</label>
                                    <input type="date" class="form-control @error('next_payment_due') is-invalid @enderror" 
                                           id="next_payment_due" name="next_payment_due" 
                                           value="{{ old('next_payment_due', $tenant->next_payment_due ? $tenant->next_payment_due->format('Y-m-d') : '') }}">
                                    @error('next_payment_due')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Tenant is Active
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Tenant
                            </button>
                            <a href="{{ route('landlord.tenants.show', $tenant) }}" class="btn btn-secondary">
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
                    <h6 class="m-0 font-weight-bold text-primary">Tenant Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        <span class="badge {{ $tenant->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Payment Status:</strong><br>
                        <span class="badge {{ $tenant->payment_status === 'active' ? 'badge-success' : ($tenant->payment_status === 'suspended' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($tenant->payment_status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $tenant->created_at->format('M d, Y') }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        {{ $tenant->updated_at->format('M d, Y') }}
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Packages</h6>
                </div>
                <div class="card-body">
                    @foreach($packages as $package)
                        <div class="mb-2 p-2 border rounded {{ $tenant->subscription_plan === $package->name ? 'bg-light' : '' }}">
                            <strong>{{ ucfirst($package->name) }}</strong><br>
                            <small class="text-muted">R{{ number_format($package->price, 2) }}/month</small>
                            @if($tenant->subscription_plan === $package->name)
                                <span class="badge badge-primary badge-sm">Current</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('subscription_plan').addEventListener('change', function() {
    const packages = @json($packages);
    const selectedPackage = packages.find(p => p.name === this.value);
    if (selectedPackage) {
        document.getElementById('monthly_fee').value = selectedPackage.price;
    }
});
</script>
@endsection
