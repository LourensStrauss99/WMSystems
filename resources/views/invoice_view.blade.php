{{-- filepath: resources/views/invoice_view.blade.php --}}
@extends('layouts.inv')

@section('content')
    <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
            <div>
                <img src="{{ asset('silogo.jpg') }}" alt="Company Logo" style="max-width: 150px;">
            </div>
            <div style="text-align: right;">
                <h2>{{ $company->company_name }}</h2>
                <div>{{ $company->address }}, {{ $company->city }}, {{ $company->province }}, {{ $company->postal_code }}, {{ $company->country }}</div>
                <div>Tel: {{ $company->company_telephone }} | Email: {{ $company->company_email }}</div>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 24px;">
            <div>
                <h5>Invoice To:</h5>
                <div>{{ $jobcard->client->name }}</div>
                <div>{{ $jobcard->client->address }}</div>
                <div>{{ $jobcard->client->email }}</div>
                <div>{{ $jobcard->client->telephone }}</div>
            </div>
            <div style="text-align: right;">
                <h5>Invoice #: {{ $jobcard->jobcard_number }}</h5>
                <div>Date: {{ $jobcard->job_date }}</div>
            </div>
        </div>
<!-- ...client and invoice header... -->

{{-- Work Done Section --}}
@if(!empty($jobcard->work_done))
    <h5>Work Done</h5>
    <div style="margin-bottom: 1.5em;">
        {{ $jobcard->work_done }}
    </div>
@endif

<!-- ...invoice details table... -->
        <h5>Invoice Details</h5>
        <table>
            <thead>
                <tr>
                    <th>Item / Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
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
                        <td class="text-right">{{ $item->pivot->quantity }}</td>
                        <td class="text-right">R {{ number_format($item->selling_price, 2) }}</td>
                        <td class="text-right">R {{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="3" class="text-right">Inventory Subtotal</th>
                    <th class="text-right">R {{ number_format($inventoryTotal, 2) }}</th>
                </tr>
                @php
                    $labourHours = $jobcard->employees->sum(fn($employee) => $employee->pivot->hours_worked ?? 0);
                    $labourTotal = $labourHours * $company->labour_rate;
                    $subtotal = $inventoryTotal + $labourTotal;
                    $vat = $subtotal * ($company->vat_percent / 100);
                    $grandTotal = $subtotal + $vat;
                @endphp
                <tr>
                    <th colspan="4" class="text-left" style="background:#f8f9fa;">Labour</th>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-right">{{ number_format($labourHours, 2) }}</td>
                    <td class="text-right">R {{ number_format($company->labour_rate, 2) }}</td>
                    <td class="text-right">R {{ number_format($labourTotal, 2) }}</td>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Subtotal:</th>
                    <td class="text-right">R {{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">VAT ({{ $company->vat_percent }}%):</th>
                    <td class="text-right">R {{ number_format($vat, 2) }}</td>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <td class="text-right"><strong>R {{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <h5>Banking Details</h5>
        <div>Bank: {{ $company->bank_name }}</div>
        <div>Account Holder: {{ $company->account_holder }}</div>
        <div>Account Number: {{ $company->account_number }}</div>
        <div>Branch Code: {{ $company->branch_code }}</div>
        <div>SWIFT/BIC: {{ $company->swift_code }}</div>

        <div style="margin-top: 24px;">
            <strong>Terms:</strong> {{ $company->invoice_terms }}
        </div>
        <div style="margin-top: 8px;">
            <em>{{ $company->invoice_footer }}</em>
        </div>

        

        <div class="no-print" style="margin-top: 24px;">
            <form method="POST" action="{{ route('invoice.email', $jobcard->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="invoice-btn">Email Invoice</button>
            </form>
            <button type="button" class="invoice-btn" onclick="window.print()">Print Invoice</button>
            <a href="{{ route('invoice.index') }}" class="invoice-btn">Back to Invoices</a>
        </div>
    </div>
@endsection

<style>
@media print {
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-width: 0 !important;
    }
    body {
        box-shadow: none !important;
    }
    .container, .bg-white, .shadow, .rounded, .p-5 {
        box-shadow: none !important;
        border-radius: 0 !important;
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .row, .col-md-3, .col-md-6, .col-md-9, .col-md-12 {
        float: none !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .sidebar, .sidebar-wrapper, nav, .navbar, .btn, .alert, .footer, .no-print, form, .mt-4, .mb-3, .d-flex {
        display: none !important;
    }
    /* Prevent page breaks inside the invoice */
    .container, .invoice-main {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
}
@page {
    size: A4;
    margin: 16mm 12mm 16mm 12mm;
}
</style>