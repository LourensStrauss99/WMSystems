{{-- filepath: resources/views/invoice_view.blade.php --}}
@extends('layouts.app')


@section('content')
<div class="container bg-white p-5 rounded shadow">
    <div class="row mb-4">
        <div class="col-md-3">
            <img src="{{ asset('silogo.jpg') }}" alt="Company Logo" style="max-width: 150px;">
        </div>
        <div class="col-md-9 text-right">
            <h2>{{ $company->company_name }}</h2>
            <div>{{ $company->address }}, {{ $company->city }}, {{ $company->province }}, {{ $company->postal_code }}, {{ $company->country }}</div>
            <div>Tel: {{ $company->company_telephone }} | Email: {{ $company->company_email }}</div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Invoice To:</h5>
            <div>{{ $jobcard->client->name }}</div>
            <div>{{ $jobcard->client->address }}</div>         {{-- Client address --}}
            <div>{{ $jobcard->client->email }}</div>           {{-- Client email --}}
            <div>{{ $jobcard->client->telephone }}</div>
        </div>
        <div class="col-md-6 text-right">
            <h5>Invoice #: {{ $jobcard->jobcard_number }}</h5>
            <div>Date: {{ $jobcard->job_date }}</div>
        </div>
    </div>

    {{-- Inventory Table --}}
    <h5>Inventory Used</h5>
    <table class="table table-bordered"> 
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $inventoryTotal = 0; @endphp
            @foreach($jobcard->inventory as $item)
                @php
                    $lineTotal = $item->pivot->quantity * $item->selling_price;
                    $inventoryTotal += $lineTotal;
                @endphp
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->pivot->quantity }}</td>
                    <td>R {{ number_format($item->selling_price, 2) }}</td>
                    <td>R {{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Inventory Subtotal</th>
                <th>R {{ number_format($inventoryTotal, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @php
        $labourHours = $jobcard->time_spent / 60;
        $labourTotal = $labourHours * $company->labour_rate;
        $subtotal = $inventoryTotal + $labourTotal;
        $vat = $subtotal * ($company->vat_percent / 100);
        $grandTotal = $subtotal + $vat;
    @endphp

    <div class="mb-3">
        <strong>Labour ({{ number_format($labourHours, 2) }} hrs @ R{{ number_format($company->labour_rate, 2) }}/hr):</strong>
        R {{ number_format($labourTotal, 2) }}
    </div>

    <table class="table">
        <tr>
            <th class="text-right">Subtotal:</th>
            <td class="text-right">R {{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <th class="text-right">VAT ({{ $company->vat_percent }}%):</th>
            <td class="text-right">R {{ number_format($vat, 2) }}</td>
        </tr>
        <tr>
            <th class="text-right">Total:</th>
            <td class="text-right"><strong>R {{ number_format($grandTotal, 2) }}</strong></td>
        </tr>
    </table>

    <h5>Banking Details</h5>
    <div>Bank: {{ $company->bank_name }}</div>
    <div>Account Holder: {{ $company->account_holder }}</div>
    <div>Account Number: {{ $company->account_number }}</div>
    <div>Branch Code: {{ $company->branch_code }}</div>
    <div>SWIFT/BIC: {{ $company->swift_code }}</div>

    <div class="mt-4">
        <strong>Terms:</strong> {{ $company->invoice_terms }}
    </div>
    <div class="mt-2">
        <em>{{ $company->invoice_footer }}</em>
    </div>

    <div class="mt-4 d-flex gap-2">
        <form method="POST" action="{{ route('invoice.email', $jobcard->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary">Email Invoice</button>
        </form>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Print Invoice</button>
    </div>
    <a href="{{ route('invoice.index') }}" class="btn btn-secondary no-print mb-3">Back to Invoices</a>
</div>
@endsection

<style>
@media print {
    body {
        background: #fff !important;
    }
    .container {
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 210mm !important;
        min-height: 297mm !important;
        max-width: 210mm !important;
    }
    .no-print {
        display: none !important;
    }
    nav, .navbar, .btn, .alert, .footer {
        display: none !important;
    }
    .page-break {
        page-break-before: always;
    }
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    html, body {
        width: 210mm;
        min-height: 297mm;
        margin: 0;
        padding: 0;
    }
}
@page {
    size: A4;
    margin: 16mm 12mm 16mm 12mm;
}
</style>