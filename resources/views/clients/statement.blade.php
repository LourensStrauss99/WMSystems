@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Statement for {{ $client->name }}</h2>
    <form method="get" class="mb-3">
        <label>Period:</label>
        <input type="date" name="start_date" value="{{ $start->toDateString() }}">
        <input type="date" name="end_date" value="{{ $end->toDateString() }}">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    <div class="mb-3">
        <strong>Opening Balance:</strong> R{{ number_format($openingBalance, 2) }}
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Invoice #</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $start->toDateString() }}</td>
                <td>Opening Balance</td>
                <td></td>
                <td></td>
                <td></td>
                <td>R{{ number_format($openingBalance, 2) }}</td>
            </tr>
            @php $balance = $openingBalance; @endphp
            @foreach($invoices as $invoice)
                @php $balance += $invoice->total; @endphp
                <tr>
                    <td>{{ $invoice->date }}</td>
                    <td>Invoice Issued</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>R{{ number_format($invoice->total, 2) }}</td>
                    <td></td>
                    <td>R{{ number_format($balance, 2) }}</td>
                </tr>
            @endforeach
            @foreach($payments as $payment)
                @php $balance -= $payment->amount; @endphp
                <tr>
                    <td>{{ $payment->date }}</td>
                    <td>Payment Received</td>
                    <td>{{ $payment->invoice_number ?? '' }}</td>
                    <td></td>
                    <td>R{{ number_format($payment->amount, 2) }}</td>
                    <td>R{{ number_format($balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mb-3">
        <strong>Closing Balance:</strong> R{{ number_format($closingBalance, 2) }}
    </div>
</div>
@endsection
