@extends('layouts.mobile')

@section('header', 'Edit Quote')

@section('content')
    <h2>Edit Quote</h2>
    <form method="POST" action="{{ route('mobile.quotes.update', $quote->id) }}">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 1rem;">Quote #: <input type="text" value="{{ $quote->quote_number }}" readonly style="border:none; background:transparent;"></div>
        <div style="margin-bottom: 1rem;">Date: <input type="date" name="quote_date" value="{{ old('quote_date', $quote->quote_date ? $quote->quote_date->format('Y-m-d') : '') }}" style="width: 60%;"></div>
        <div style="margin-bottom: 1rem;">Client Name: <input type="text" name="client_name" value="{{ $quote->client_name }}" style="width: 60%;"></div>
        <div style="margin-bottom: 1rem;">Client Address: <input type="text" name="client_address" value="{{ $quote->client_address }}" style="width: 60%;"></div>
        <div style="margin-bottom: 1rem;">Client Email: <input type="email" name="client_email" value="{{ $quote->client_email }}" style="width: 60%;"></div>
        <div style="margin-bottom: 1rem;">Client Telephone: <input type="text" name="client_telephone" value="{{ $quote->client_telephone }}" style="width: 60%;"></div>
        <h5 class="mt-3">Quote Items</h5>
        <table style="width:100%; font-size:0.95rem; margin-bottom:1rem;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $i => $item)
                    <tr>
                        <td><input type="text" name="items[{{ $i }}][description]" value="{{ $item['description'] ?? '' }}" style="width:100%;"></td>
                        <td><input type="number" name="items[{{ $i }}][qty]" value="{{ $item['qty'] ?? $item['quantity'] ?? '' }}" style="width:60px;"></td>
                        <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" style="width:80px;"></td>
                        <td><input type="number" step="0.01" name="items[{{ $i }}][total]" value="{{ $item['total'] ?? '' }}" style="width:80px;"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-bottom: 1rem;">Notes / Terms:<br>
            <textarea name="notes" style="width:100%; min-height:60px;">{{ $quote->notes }}</textarea>
        </div>
        <button type="submit" style="background: #2563eb; color: #fff; border: none; border-radius: 4px; padding: 0.6rem 1.2rem; font-size: 1rem;">Save</button>
        <a href="{{ route('mobile.quotes.show', $quote->id) }}" style="margin-left: 1rem; color: #2563eb; text-decoration: underline;">Cancel</a>
    </form>
@endsection 