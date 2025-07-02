{{-- filepath: resources/views/suppliers/edit.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2><i class="fas fa-edit me-2"></i>Edit Supplier</h2>
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @csrf
        @method('PUT')
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    @foreach(['name','contact_person','email','phone','address','city','postal_code','vat_number','account_number','credit_limit'] as $field)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ ucwords(str_replace('_',' ',$field)) }}</label>
                            <input type="{{ $field === 'email' ? 'email' : 'text' }}" 
                                   class="form-control @error($field) is-invalid @enderror"
                                   name="{{ $field }}" value="{{ old($field, $supplier->$field) }}">
                            @error($field)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Payment Terms</label>
                        <select name="payment_terms" class="form-select @error('payment_terms') is-invalid @enderror">
                            @foreach(\App\Models\Supplier::getPaymentTermsOptions() as $key => $label)
                                <option value="{{ $key }}" {{ old('payment_terms', $supplier->payment_terms) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Active</label>
                        <select name="active" class="form-select @error('active') is-invalid @enderror">
                            <option value="1" {{ old('active', $supplier->active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('active', $supplier->active) ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i>Save Changes
            </button>
            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection