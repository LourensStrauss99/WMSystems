@extends('layouts.app')

@section('content')
<div id="print-area" class="container bg-white p-5 rounded shadow" style="max-width: 900px; margin: auto; font-family: 'Arial', sans-serif;">
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
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Quote To:</strong>
            <div>{{ $quote->client_name }}</div>
            <div>{{ $quote->client_address }}</div>
            <div>{{ $quote->client_email }}</div>
            <div>{{ $quote->client_telephone }}</div>
        </div>
        <div class="col-md-6 text-right">
            <strong>Quote #:</strong> {{ $quote->quote_number }}<br>
            <strong>Date:</strong> {{ $quote->quote_date }}
        </div>
    </div>
    <h5 class="mt-4">Quote Items</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
                @if(isset($item['description']))
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        <td>{{ $item['qty'] ?? '' }}</td>
                        <td>R {{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                        <td>R {{ number_format($item['total'] ?? 0, 2) }}</td>
                    </tr>
                @elseif(isset($item['inventory_id']))
                    @php
                        $inv = \App\Models\Inventory::find($item['inventory_id']);
                    @endphp
                    <tr>
                        <td>
                            @if($inv)
                                [{{ $inv->short_code }}] {{ $inv->description }}
                            @else
                                Inventory #{{ $item['inventory_id'] }}
                            @endif
                        </td>
                        <td>{{ $item['quantity'] ?? '' }}</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <div class="mb-3">
        <strong>Notes / Terms:</strong>
        <div>{{ $quote->notes }}</div>
    </div>
    <div class="d-flex gap-2 mt-4 no-print">
        <a href="{{ route('quotes.index') }}" class="btn btn-secondary">Back to Quotes</a>
        <button type="button" class="btn btn-secondary no-print" onclick="printQuote()">Print Quote</button>
    </div>
</div>
@endsection

<style>
@media print {
    body, html {
        width: 210mm;
        min-height: 297mm;
        margin: 0;
        padding: 0;
        background: #fff !important;
    }
    #print-area {
        width: 210mm !important;
        min-height: 297mm !important;
        max-width: 210mm !important;
        margin: 0 !important;
        padding: 16mm 12mm 16mm 12mm !important;
        box-shadow: none !important;
        display: block !important;
    }
    .no-print, nav, .navbar, .btn, .alert, .footer, header, footer, .page-title, .page-header {
        display: none !important;
    }
    /* Hide everything except #print-area */
    /* #app > *:not(#print-area) {
        display: none !important;
    }*/
}
@page {
    size: A4;
    margin: 16mm 12mm 16mm 12mm;
}
</style>

<script>
function printQuote() {
    window.print();
}
</script>