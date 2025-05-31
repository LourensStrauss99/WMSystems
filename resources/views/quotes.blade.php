@extends('layouts.app')
@extends('layouts.nav')
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

    <form method="POST" action="{{ route('quotes.save') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Quote To:</label>
                <input type="text" class="form-control" name="client_name" value="{{ old('client_name', $quote->client_name ?? '') }}">
                <input type="text" class="form-control mt-1" name="client_address" value="{{ old('client_address', $quote->client_address ?? '') }}">
                <input type="email" class="form-control mt-1" name="client_email" value="{{ old('client_email', $quote->client_email ?? '') }}">
                <input type="text" class="form-control mt-1" name="client_telephone" value="{{ old('client_telephone', $quote->client_telephone ?? '') }}">
            </div>
            <div class="col-md-6 text-right">
                <label>Quote #:</label>
                <input type="text" class="form-control" name="quote_number" value="{{ old('quote_number', $quote->quote_number ?? $nextQuoteNumber) }}" readonly>
                <label>Date:</label>
                <input type="date" class="form-control" name="quote_date" value="{{ old('quote_date', $quote->quote_date ?? now()->toDateString()) }}">
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
                {{-- Loop through quote items --}}
                @foreach(old('items', $quote->items ?? [ ['description'=>'', 'qty'=>'', 'unit_price'=>'', 'total'=>''] ]) as $i => $item)
                <tr>
                    <td><input type="text" name="items[{{ $i }}][description]" class="form-control" value="{{ $item['description'] }}"></td>
                    <td><input type="number" name="items[{{ $i }}][qty]" class="form-control" value="{{ $item['qty'] }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control" value="{{ $item['unit_price'] }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control" value="{{ $item['total'] }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{-- Add JS to add/remove rows as needed --}}

        <div class="mb-3">
            <label>Notes / Terms</label>
            <textarea class="form-control" name="notes">{{ old('notes', $quote->notes ?? '') }}</textarea>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-success">Save Quote</button>
            <!--<button type="button" class="btn btn-secondary" onclick="printQuote()">Print Quote</button>-->
            <a href="{{ route('quotes.index') }}" class="btn btn-secondary no-print">Back to Quotes</a>
            {{-- Add Email button if needed --}}
        </div>
    </form>

    <form method="GET" action="{{ route('quotes.index') }}" class="mb-3">
        <input type="text" name="client" class="form-control" placeholder="Search by client name" value="{{ request('client') }}">
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <div class="quote-list mt-4">
        @foreach($quotes as $quote)
            <div 
                ondblclick="window.location='{{ route('quotes.show', $quote->id) }}'" 
                class="quote-list-item"
                style="cursor:pointer;"
            >
                Quote #{{ $quote->quote_number }} - {{ $quote->quote_date }}
            </div>
        @endforeach
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
    }
    .no-print, nav, .navbar, .btn, .alert, .footer {
        display: none !important;
    }
}
@page {
    size: A4;
    margin: 16mm 12mm 16mm 12mm;
}
</style>

<script>
function printQuote() {
    var printContents = document.getElementById('print-area').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // Optional: reload to restore JS events
}
</script>