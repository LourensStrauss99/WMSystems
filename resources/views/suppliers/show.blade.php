{{-- filepath: resources/views/suppliers/show.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>
            <i class="fas fa-industry me-2"></i>Supplier Details
        </h2>
        <div>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">{{ $supplier->name }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Contact Person:</strong> {{ $supplier->contact_person }}</p>
                    <p><strong>Email:</strong> {{ $supplier->email }}</p>
                    <p><strong>Phone:</strong> {{ $supplier->phone }}</p>
                    <p><strong>Address:</strong> {{ $supplier->address }}</p>
                    <p><strong>City:</strong> {{ $supplier->city }}</p>
                    <p><strong>Postal Code:</strong> {{ $supplier->postal_code }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>VAT Number:</strong> {{ $supplier->vat_number }}</p>
                    <p><strong>Account Number:</strong> {{ $supplier->account_number }}</p>
                    <p><strong>Credit Limit:</strong> {{ $supplier->formatted_credit_limit }}</p>
                    <p><strong>Payment Terms:</strong> {{ $supplier->payment_terms_text }}</p>
                    <p><strong>Status:</strong> {!! $supplier->status_badge !!}</p>
                    <p><strong>Created At:</strong> {{ $supplier->created_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection