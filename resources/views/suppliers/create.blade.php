{{-- filepath: resources/views/suppliers/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold">
                <i class="fas fa-building me-2 text-success"></i>
                Add New Supplier
            </h2>
            <p class="text-muted">Create a new supplier for purchase orders and inventory management</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Suppliers
            </a>
        </div>
    </div>

    <!-- Supplier Form -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-plus-circle me-2"></i>Supplier Information
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf
                
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-success fw-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle me-1"></i>Basic Information
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">
                            <i class="fas fa-building me-1"></i>Company Name *
                        </label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="contact_person" class="form-label fw-bold">
                            <i class="fas fa-user me-1"></i>Contact Person
                        </label>
                        <input type="text" name="contact_person" id="contact_person" 
                               class="form-control @error('contact_person') is-invalid @enderror" 
                               value="{{ old('contact_person') }}">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-success fw-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-phone me-1"></i>Contact Information
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">
                            <i class="fas fa-envelope me-1"></i>Email Address
                        </label>
                        <input type="email" name="email" id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-bold">
                            <i class="fas fa-phone me-1"></i>Phone Number
                        </label>
                        <input type="text" name="phone" id="phone" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-success fw-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i>Address Information
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="address" class="form-label fw-bold">
                            <i class="fas fa-map me-1"></i>Physical Address
                        </label>
                        <textarea name="address" id="address" rows="3" 
                                  class="form-control @error('address') is-invalid @enderror" 
                                  placeholder="Enter full physical address">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="city" class="form-label fw-bold">
                            <i class="fas fa-city me-1"></i>City
                        </label>
                        <input type="text" name="city" id="city" 
                               class="form-control @error('city') is-invalid @enderror" 
                               value="{{ old('city') }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="postal_code" class="form-label fw-bold">
                            <i class="fas fa-mail-bulk me-1"></i>Postal Code
                        </label>
                        <input type="text" name="postal_code" id="postal_code" 
                               class="form-control @error('postal_code') is-invalid @enderror" 
                               value="{{ old('postal_code') }}">
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Business Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-success fw-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-briefcase me-1"></i>Business Information
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="vat_number" class="form-label fw-bold">
                            <i class="fas fa-receipt me-1"></i>VAT Number
                        </label>
                        <input type="text" name="vat_number" id="vat_number" 
                               class="form-control @error('vat_number') is-invalid @enderror" 
                               value="{{ old('vat_number') }}" placeholder="e.g., 4123456789">
                        @error('vat_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="account_number" class="form-label fw-bold">
                            <i class="fas fa-university me-1"></i>Account Number
                        </label>
                        <input type="text" name="account_number" id="account_number" 
                               class="form-control @error('account_number') is-invalid @enderror" 
                               value="{{ old('account_number') }}">
                        @error('account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-success fw-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-money-bill-wave me-1"></i>Financial Terms
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="credit_limit" class="form-label fw-bold">
                            <i class="fas fa-credit-card me-1"></i>Credit Limit (R)
                        </label>
                        <input type="number" step="0.01" min="0" name="credit_limit" id="credit_limit" 
                               class="form-control @error('credit_limit') is-invalid @enderror" 
                               value="{{ old('credit_limit', 0) }}">
                        @error('credit_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="payment_terms" class="form-label fw-bold">
                            <i class="fas fa-calendar me-1"></i>Payment Terms *
                        </label>
                        <select name="payment_terms" id="payment_terms" 
                                class="form-select @error('payment_terms') is-invalid @enderror" required>
                            <option value="">Select Payment Terms</option>
                            <option value="cash" {{ old('payment_terms') == 'cash' ? 'selected' : '' }}>
                                Cash on Delivery
                            </option>
                            <option value="30_days" {{ old('payment_terms') == '30_days' ? 'selected' : '' }}>
                                30 Days
                            </option>
                            <option value="60_days" {{ old('payment_terms') == '60_days' ? 'selected' : '' }}>
                                60 Days
                            </option>
                            <option value="90_days" {{ old('payment_terms') == '90_days' ? 'selected' : '' }}>
                                90 Days
                            </option>
                        </select>
                        @error('payment_terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active" id="active" 
                                   value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="active">
                                <i class="fas fa-toggle-on me-1"></i>Active Supplier
                            </label>
                            <small class="form-text text-muted">
                                Only active suppliers will appear in purchase order dropdowns
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Create Supplier
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.btn-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
}
</style>
@endsection