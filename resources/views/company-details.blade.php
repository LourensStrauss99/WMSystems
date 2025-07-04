<!-- filepath: resources/views/company-details.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-building text-primary me-2"></i>
                Company Details & Business Settings
            </h2>
            <p class="text-muted">Configure your company information and business rates for all documentation</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('master.settings') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Settings
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
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('company.details.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Company Information Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Company Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Company Name *</label>
                                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $company->company_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Trading Name</label>
                                <input type="text" name="trading_name" class="form-control" value="{{ old('trading_name', $company->trading_name) }}">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Company Registration Number</label>
                                <input type="text" name="company_reg_number" class="form-control" value="{{ old('company_reg_number', $company->company_reg_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">VAT Registration Number</label>
                                <input type="text" name="vat_reg_number" class="form-control" value="{{ old('vat_reg_number', $company->vat_reg_number) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">PAYE Number</label>
                                <input type="text" name="paye_number" class="form-control" value="{{ old('paye_number', $company->paye_number) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">UIF Number</label>
                                <input type="text" name="uif_number" class="form-control" value="{{ old('uif_number', $company->uif_number) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">B-BBEE Level</label>
                                <select name="bee_level" class="form-select">
                                    <option value="">-- Select Level --</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="Level {{ $i }}" {{ old('bee_level', $company->bee_level) == "Level $i" ? 'selected' : '' }}>Level {{ $i }}</option>
                                    @endfor
                                    <option value="Non-Compliant" {{ old('bee_level', $company->bee_level) == 'Non-Compliant' ? 'selected' : '' }}>Non-Compliant</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Company Logo</label>
                        <div class="text-center">
                            @if($company->company_logo)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $company->company_logo) }}" alt="Company Logo" class="img-thumbnail" style="max-height: 150px;">
                                    <div class="mt-2">
                                        <a href="{{ route('company.remove-logo') }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove logo?')">
                                            <i class="fas fa-trash me-1"></i>Remove Logo
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="border rounded p-4 mb-3 bg-light">
                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">No logo uploaded</p>
                                </div>
                            @endif
                            <input type="file" name="company_logo" class="form-control" accept="image/*">
                            <small class="text-muted">Max 2MB, JPG/PNG formats</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-address-book me-2"></i>Contact Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Primary Phone *</label>
                        <input type="text" name="company_telephone" class="form-control" value="{{ old('company_telephone', $company->company_telephone) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Mobile/Cell</label>
                        <input type="text" name="company_cell" class="form-control" value="{{ old('company_cell', $company->company_cell) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fax</label>
                        <input type="text" name="company_fax" class="form-control" value="{{ old('company_fax', $company->company_fax) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">General Email *</label>
                        <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $company->company_email) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Accounts Email</label>
                        <input type="email" name="accounts_email" class="form-control" value="{{ old('accounts_email', $company->accounts_email) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Orders Email</label>
                        <input type="email" name="orders_email" class="form-control" value="{{ old('orders_email', $company->orders_email) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Support Email</label>
                        <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $company->support_email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Website</label>
                        <input type="url" name="company_website" class="form-control" value="{{ old('company_website', $company->company_website) }}" placeholder="https://www.yourcompany.com">
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Physical Address *</label>
                        <textarea name="physical_address" class="form-control" rows="3" required>{{ old('physical_address', $company->physical_address) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Postal Address</label>
                        <textarea name="postal_address" class="form-control" rows="3">{{ old('postal_address', $company->postal_address) }}</textarea>
                        <small class="text-muted">Leave blank to use physical address</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">City *</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $company->city) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Province</label>
                        <select name="province" class="form-select">
                            <option value="">-- Select Province --</option>
                            @php
                                $provinces = ['Gauteng', 'Western Cape', 'KwaZulu-Natal', 'Eastern Cape', 'Free State', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape'];
                            @endphp
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" {{ old('province', $company->province) == $province ? 'selected' : '' }}>{{ $province }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $company->postal_code) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Country *</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $company->country) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Rates Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>Business Rates & Pricing
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Standard Labour Rate *</label>
                        <div class="input-group">
                            <span class="input-group-text">R</span>
                            <input type="number" step="0.01" name="labour_rate" class="form-control" value="{{ old('labour_rate', $company->labour_rate) }}" required>
                            <span class="input-group-text">/hour</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Call-Out Rate</label>
                        <div class="input-group">
                            <span class="input-group-text">R</span>
                            <input type="number" step="0.01" name="call_out_rate" class="form-control" value="{{ old('call_out_rate', $company->call_out_rate) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">VAT Percentage *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="vat_percent" class="form-control" value="{{ old('vat_percent', $company->vat_percent) }}" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Default Markup</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="markup_percentage" class="form-control" value="{{ old('markup_percentage', $company->markup_percentage) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Overtime Multiplier</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="overtime_multiplier" class="form-control" value="{{ old('overtime_multiplier', $company->overtime_multiplier) }}">
                            <span class="input-group-text">x</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Weekend Multiplier</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="weekend_multiplier" class="form-control" value="{{ old('weekend_multiplier', $company->weekend_multiplier) }}">
                            <span class="input-group-text">x</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Public Holiday Multiplier</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="public_holiday_multiplier" class="form-control" value="{{ old('public_holiday_multiplier', $company->public_holiday_multiplier) }}">
                            <span class="input-group-text">x</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Mileage Rate</label>
                        <div class="input-group">
                            <span class="input-group-text">R</span>
                            <input type="number" step="0.01" name="mileage_rate" class="form-control" value="{{ old('mileage_rate', $company->mileage_rate) }}">
                            <span class="input-group-text">/km</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banking Information Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-university me-2"></i>Banking Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $company->bank_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Account Holder</label>
                        <input type="text" name="account_holder" class="form-control" value="{{ old('account_holder', $company->account_holder) }}">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Account Number</label>
                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $company->account_number) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Branch Code</label>
                        <input type="text" name="branch_code" class="form-control" value="{{ old('branch_code', $company->branch_code) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Branch Name</label>
                        <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name', $company->branch_name) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Account Type</label>
                        <select name="account_type" class="form-select">
                            <option value="">-- Select Type --</option>
                            <option value="Current" {{ old('account_type', $company->account_type) == 'Current' ? 'selected' : '' }}>Current Account</option>
                            <option value="Savings" {{ old('account_type', $company->account_type) == 'Savings' ? 'selected' : '' }}>Savings Account</option>
                            <option value="Business" {{ old('account_type', $company->account_type) == 'Business' ? 'selected' : '' }}>Business Account</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">SWIFT Code</label>
                        <input type="text" name="swift_code" class="form-control" value="{{ old('swift_code', $company->swift_code) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Terms Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-contract me-2"></i>Business Terms & Policies
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Default Payment Terms</label>
                        <div class="input-group">
                            <input type="number" name="default_payment_terms" class="form-control" value="{{ old('default_payment_terms', $company->default_payment_terms) }}">
                            <span class="input-group-text">days</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Quote Validity</label>
                        <div class="input-group">
                            <input type="number" name="quote_validity_days" class="form-control" value="{{ old('quote_validity_days', $company->quote_validity_days) }}">
                            <span class="input-group-text">days</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Invoice Terms & Conditions</label>
                        <textarea name="invoice_terms" class="form-control" rows="3">{{ old('invoice_terms', $company->invoice_terms) }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Quote Terms</label>
                        <textarea name="quote_terms" class="form-control" rows="3">{{ old('quote_terms', $company->quote_terms) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Warranty Terms</label>
                        <textarea name="warranty_terms" class="form-control" rows="3">{{ old('warranty_terms', $company->warranty_terms) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Save Company Details
                </button>
            </div>
        </div>
    </form>
</div>
@endsection