{{-- filepath: resources/views/customer-show.blade.php --}}
@extends('layouts.auth')

@section('content')
<div class="container mt-4">
    <a href="{{ route('customers.index') }}" class="btn btn-light mb-3" title="Back to Customers">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16" style="vertical-align: middle;">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
        <span class="ms-2">Back to Customers</span>
    </a>
    <h2>Customer Details</h2>
    <div>
        <strong>Name:</strong> {{ $customer->name }} {{ $customer->surname }}<br>
        <strong>Telephone:</strong> {{ $customer->telephone }}<br>
        <strong>Address:</strong> {{ $customer->address }}<br>
        <strong>Email:</strong> {{ $customer->email }}<br>
        <strong>Client Since:</strong> {{ $customer->created_at->format('Y-m-d') }}
    </div>

    <hr>

    <h4>Work Done History</h4>
    <ul>
        @foreach($workHistory as $jobcard)
            <li>
                {{ $jobcard->created_at->format('Y-m-d') }}: {{ $jobcard->work_done ?? 'No details' }}
            </li>
        @endforeach
    </ul>

    <hr>

    <h4>Invoice History</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Paid Date</th>
                <th>Credit Age</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoiceHistory as $invoice)
                @php
                    $days = $invoice->payment_date
                        ? \Carbon\Carbon::parse($invoice->payment_date)->diffInDays($invoice->invoice_date)
                        : \Carbon\Carbon::now()->diffInDays($invoice->invoice_date);

                    $days = abs((int) $days);

                    if ($days <= 30) {
                        $color = 'bg-success-subtle text-success-emphasis'; // light green
                    } elseif ($days <= 60) {
                        $color = 'bg-warning-subtle text-warning-emphasis'; // light orange/yellow
                    } elseif ($days <= 90) {
                        $color = 'bg-danger-subtle text-danger-emphasis'; // light red
                    } elseif ($days <= 120) {
                        $color = 'bg-danger text-white'; // solid red
                    } else {
                        $color = 'bg-dark text-danger fw-bold'; // black with bold red text
                    }
                @endphp
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->invoice_date }}</td>
                    <td>{{ ucfirst($invoice->status) }}</td>
                    <td>{{ $invoice->amount ?? '-' }}</td>
                    <td>{{ $invoice->payment_date ?? '-' }}</td>
                    <td class="{{ $color }}">{{ $days }} days</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection