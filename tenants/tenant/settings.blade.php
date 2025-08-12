@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Company Settings</h1>
                    <p class="text-muted mb-0">Manage your company information and preferences</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
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
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-building me-2"></i>Company Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('tenant.update-settings') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label fw-bold">
                                            <i class="fas fa-briefcase me-1"></i>Company Name
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('company_name') is-invalid @enderror" 
                                               id="company_name" 
                                               name="company_name" 
                                               value="{{ old('company_name', $tenant->company_name) }}" 
                                               required>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="owner_name" class="form-label fw-bold">
                                            <i class="fas fa-user-tie me-1"></i>Owner Name
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('owner_name') is-invalid @enderror" 
                                               id="owner_name" 
                                               name="owner_name" 
                                               value="{{ old('owner_name', $tenant->owner_name) }}" 
                                               required>
                                        @error('owner_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label fw-bold">
                                            <i class="fas fa-phone me-1"></i>Phone Number
                                        </label>
                                        <input type="tel" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone', $tenant->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold text-muted">Owner Email</label>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               value="{{ $tenant->owner_email }}" 
                                               readonly>
                                        <small class="text-muted">Email cannot be changed from here</small>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="address" class="form-label fw-bold">
                                            <i class="fas fa-map-marker-alt me-1"></i>Address
                                        </label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" 
                                                  name="address" 
                                                  rows="3">{{ old('address', $tenant->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Settings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Account Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Account Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Company Slug</label>
                                <div class="fw-bold">{{ $tenant->slug }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Database</label>
                                <div class="small"><code>{{ $tenant->database_name }}</code></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Status</label>
                                <div>
                                    @if($tenant->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($tenant->status === 'suspended')
                                        <span class="badge bg-danger">Suspended</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($tenant->status) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-muted small">Member Since</label>
                                <div>{{ $tenant->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Information -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-credit-card me-2"></i>Subscription
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Plan</label>
                                <div>
                                    @if($tenant->subscription_status === 'trial')
                                        <span class="badge bg-info">Free Trial</span>
                                    @elseif($tenant->subscription_status === 'active')
                                        <span class="badge bg-success">Active Plan</span>
                                    @elseif($tenant->subscription_status === 'expired')
                                        <span class="badge bg-danger">Expired</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($tenant->subscription_status) }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($tenant->subscription_ends_at)
                            <div class="mb-3">
                                <label class="form-label text-muted small">
                                    @if($tenant->subscription_status === 'trial')
                                        Trial Ends
                                    @else
                                        Renewal Date
                                    @endif
                                </label>
                                <div>{{ $tenant->subscription_ends_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $tenant->subscription_ends_at->diffForHumans() }}</small>
                            </div>
                            @endif
                            @if($tenant->subscription_status === 'trial')
                            <div class="d-grid">
                                <button class="btn btn-sm btn-outline-primary" disabled>
                                    <i class="fas fa-crown me-1"></i>Upgrade Plan
                                </button>
                                <small class="text-muted mt-1">Coming soon</small>
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
