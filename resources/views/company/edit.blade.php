{{-- filepath: resources/views/company/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-building text-primary me-2"></i>
                Company Details
            </h2>
            <p class="text-muted">Manage your company information and settings</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('master.settings') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Settings
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('company.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Company Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Company Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">
                                    <i class="fas fa-building me-1"></i>Company Name *
                                </label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" 
                                       value="{{ old('company_name', $company->company_name) }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="trading_name" class="form-label">
                                    <i class="fas fa-store me-1"></i>Trading Name
                                </label>
                                <input type="text" class="form-control @error('trading_name') is-invalid @enderror" 
                                       id="trading_name" name="trading_name" 
                                       value="{{ old('trading_name', $company->trading_name) }}">
                                @error('trading_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registration_number" class="form-label">
                                    <i class="fas fa-certificate me-1"></i>Registration Number
                                </label>
                                <input type="text" class="form-control @error('registration_number') is-invalid @enderror" 
                                       id="registration_number" name="registration_number" 
                                       value="{{ old('registration_number', $company->registration_number) }}">
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vat_number" class="form-label">
                                    <i class="fas fa-receipt me-1"></i>VAT Number
                                </label>
                                <input type="text" class="form-control @error('vat_number') is-invalid @enderror" 
                                       id="vat_number" name="vat_number" 
                                       value="{{ old('vat_number', $company->vat_number) }}">
                                @error('vat_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Address Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="address_line_1" class="form-label">
                                <i class="fas fa-home me-1"></i>Address Line 1 *
                            </label>
                            <input type="text" class="form-control @error('address_line_1') is-invalid @enderror" 
                                   id="address_line_1" name="address_line_1" 
                                   value="{{ old('address_line_1', $company->address_line_1) }}" required>
                            @error('address_line_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="address_line_2" class="form-label">
                                <i class="fas fa-home me-1"></i>Address Line 2
                            </label>
                            <input type="text" class="form-control @error('address_line_2') is-invalid @enderror" 
                                   id="address_line_2" name="address_line_2" 
                                   value="{{ old('address_line_2', $company->address_line_2) }}">
                            @error('address_line_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">
                                    <i class="fas fa-city me-1"></i>City *
                                </label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" 
                                       value="{{ old('city', $company->city) }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state_province" class="form-label">
                                    <i class="fas fa-map me-1"></i>State/Province
                                </label>
                                <input type="text" class="form-control @error('state_province') is-invalid @enderror" 
                                       id="state_province" name="state_province" 
                                       value="{{ old('state_province', $company->state_province) }}">
                                @error('state_province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="postal_code" class="form-label">
                                    <i class="fas fa-mail-bulk me-1"></i>Postal Code *
                                </label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       id="postal_code" name="postal_code" 
                                       value="{{ old('postal_code', $company->postal_code) }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="country" class="form-label">
                                <i class="fas fa-globe me-1"></i>Country *
                            </label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" 
                                   value="{{ old('country', $company->country) }}" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-address-book me-2"></i>Contact Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Phone Number
                                </label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $company->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Address
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $company->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="website" class="form-label">
                                <i class="fas fa-globe me-1"></i>Website
                            </label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" 
                                   value="{{ old('website', $company->website) }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Banking Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-university me-2"></i>Banking Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">
                                <i class="fas fa-bank me-1"></i>Bank Name
                            </label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                   id="bank_name" name="bank_name" 
                                   value="{{ old('bank_name', $company->bank_name) }}">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bank_account_number" class="form-label">
                                    <i class="fas fa-credit-card me-1"></i>Account Number
                                </label>
                                <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" 
                                       id="bank_account_number" name="bank_account_number" 
                                       value="{{ old('bank_account_number', $company->bank_account_number) }}">
                                @error('bank_account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_branch_code" class="form-label">
                                    <i class="fas fa-code-branch me-1"></i>Branch Code
                                </label>
                                <input type="text" class="form-control @error('bank_branch_code') is-invalid @enderror" 
                                       id="bank_branch_code" name="bank_branch_code" 
                                       value="{{ old('bank_branch_code', $company->bank_branch_code) }}">
                                @error('bank_branch_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bank_account_type" class="form-label">
                                <i class="fas fa-list me-1"></i>Account Type
                            </label>
                            <select class="form-select @error('bank_account_type') is-invalid @enderror" 
                                    id="bank_account_type" name="bank_account_type">
                                <option value="">Select Account Type</option>
                                <option value="cheque" {{ old('bank_account_type', $company->bank_account_type) == 'cheque' ? 'selected' : '' }}>Cheque Account</option>
                                <option value="savings" {{ old('bank_account_type', $company->bank_account_type) == 'savings' ? 'selected' : '' }}>Savings Account</option>
                                <option value="current" {{ old('bank_account_type', $company->bank_account_type) == 'current' ? 'selected' : '' }}>Current Account</option>
                            </select>
                            @error('bank_account_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo and Actions -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image me-2"></i>Company Logo
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        @if($company->logo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $company->logo) }}" 
                                     alt="Company Logo" 
                                     class="img-fluid rounded border"
                                     style="max-height: 200px;">
                            </div>
                            <a href="{{ route('company.remove-logo') }}" 
                               class="btn btn-sm btn-outline-danger mb-2"
                               onclick="return confirm('Are you sure you want to remove the logo?')">
                                <i class="fas fa-trash me-1"></i>Remove Logo
                            </a>
                        @else
                            <div class="mb-3">
                                <i class="fas fa-image fa-4x text-muted"></i>
                                <p class="text-muted mt-2">No logo uploaded</p>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="logo" class="form-label">Upload New Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Recommended: PNG, JPG, GIF. Max size: 2MB
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Company Details
                            </button>
                            <a href="{{ route('master.settings') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection