@extends('layouts.mobile')

@section('header', 'Quote Details')

@section('content')
    <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1rem; margin-bottom: 1rem;">
        <div style="font-size: 1.1rem; font-weight: bold; margin-bottom: 0.5rem; color: #2563eb;">
            {{ $quote->quote_number }}
        </div>
        <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Date: {{ $quote->created_at->format('Y-m-d') }}</div>
        <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Client: {{ $quote->client_name }}</div>
        <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Address: {{ $quote->client_address }}</div>
        <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Email: {{ $quote->client_email }}</div>
        <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Telephone: {{ $quote->client_telephone }}</div>
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
                @foreach($quote->items as $item)
                    <tr>
                        <td>{{ $item['description'] ?? '' }}</td>
                        <td>{{ $item['qty'] ?? $item['quantity'] ?? '' }}</td>
                        <td>{{ isset($item['unit_price']) ? 'R ' . number_format($item['unit_price'], 2) : '' }}</td>
                        <td>{{ isset($item['total']) ? 'R ' . number_format($item['total'], 2) : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-bottom: 0.3rem;"><strong>Notes / Terms:</strong> {{ $quote->notes }}</div>
        <div style="margin-top: 1rem;">
            <a href="{{ route('mobile.quotes.edit', $quote->id) }}" style="background: #2563eb; color: #fff; border: none; border-radius: 4px; padding: 0.4rem 1rem; text-decoration: none; font-size: 0.95rem;">Edit</a>
        </div>
    </div>
@endsection 