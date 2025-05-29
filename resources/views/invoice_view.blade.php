{{-- filepath: resources/views/invoice_view.blade.php --}}
@extends('layouts.app')
@extends('layouts.nav')

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
</div>
@endsection