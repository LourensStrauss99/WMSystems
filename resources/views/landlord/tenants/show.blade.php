@extends('layouts.app')

@section('title', 'Tenant Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tenant Details: {{ $tenant->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.tenants') }}" class="text-decoration-none">Tenants</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $tenant->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('landlord.tenants.edit', $tenant) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Tenant
            </a>
            <a href="{{ route('landlord.tenants') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tenants
            </a>
        </div>
    </div>

    <!-- Tenant Information Card -->
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tenant Information</h6>
                    <span class="badge {{ $tenant->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Company Name:</strong><br>
                        {{ $tenant->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Owner Name:</strong><br>
                        {{ $tenant->owner_name }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $tenant->owner_email }}">{{ $tenant->owner_email }}</a>
                    </div>
                    @if($tenant->owner_phone)
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <a href="tel:{{ $tenant->owner_phone }}">{{ $tenant->owner_phone }}</a>
                    </div>
                    @endif
                    @if($tenant->address)
                    <div class="mb-3">
                        <strong>Address:</strong><br>
                        {{ $tenant->address }}
                        @if($tenant->city), {{ $tenant->city }}@endif
                        @if($tenant->country), {{ $tenant->country }}@endif
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Subscription Plan:</strong><br>
                        <span class="badge badge-info">{{ ucfirst($tenant->subscription_plan) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Monthly Fee:</strong><br>
                        R{{ number_format($tenant->monthly_fee, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Payment Status:</strong><br>
                        <span class="badge {{ $tenant->payment_status === 'active' ? 'badge-success' : ($tenant->payment_status === 'suspended' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($tenant->payment_status) }}
                        </span>
                    </div>
                    @if($tenant->next_payment_due)
                    <div class="mb-3">
                        <strong>Next Payment Due:</strong><br>
                        {{ \Carbon\Carbon::parse($tenant->next_payment_due)->format('M d, Y') }}
                        @if(\Carbon\Carbon::parse($tenant->next_payment_due)->isPast())
                            <span class="badge badge-danger">Overdue</span>
                        @endif
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $tenant->created_at->format('M d, Y') }}
                    </div>
                    @if($tenant->domains->count() > 0)
                    <div class="mb-3">
                        <strong>Domains:</strong><br>
                        @foreach($tenant->domains as $domain)
                            <span class="badge badge-secondary">{{ $domain->domain }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Invoiced</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">R{{ number_format($totalInvoiced, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Paid</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">R{{ number_format($totalPaid, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Outstanding</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">R{{ number_format($outstandingBalance, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Overdue</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">R{{ number_format($overdueAmount, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Invoices</h6>
                </div>
                <div class="card-body">
                    @if($recentInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentInvoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>R{{ number_format($invoice->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $invoice->status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $invoice->description }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No invoices found for this tenant.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Payments</h6>
                </div>
                <div class="card-body">
                    @if($recentPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payment #</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_reference }}</td>
                                        <td>R{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>{{ $payment->transaction_reference ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No payments found for this tenant.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
