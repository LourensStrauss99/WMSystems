@extends('layouts.app')


@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Reports</h2>
    <div class="row">
        <!-- Hours Booked -->
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header" style="background-color: #e3f2fd; color: #000;">
                    <strong>Hours booked</strong>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="hours-booked-list">
                    <div class="mb-2 font-bold">
                        Total Hours: {{ $hoursBooked }}
                    </div>
                    <ul class="list-group" id="hours-booked-jobcards-list">
                        @foreach($jobcards as $jobcard)
                            <li class="list-group-item">
                                #{{ $jobcard->jobcard_number }}<br>
                                Hours: {{ round($jobcard->time_spent / 60, 2) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- Invoiced -->
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header" style="background-color: #e3f2fd; color: #000;">
                    <strong>Invoiced</strong>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="jobcards-invoiced-list">
                    <div class="mb-2 font-bold">
                        Combined Grand Total: R{{ number_format($invoicesGrandTotal, 2) }}
                    </div>
                    <ul class="list-group" id="jobcards-invoiced-jobcards-list">
                        @foreach($invoices as $invoice)
                            <li class="list-group-item">
                                Invoice #: {{ $invoice->invoice_number ?? '-' }}<br>
                                Jobcard #: {{ $invoice->jobcard->jobcard_number ?? '-' }}<br>
                                Amount: R{{ number_format($invoice->amount, 2) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- Inventory Invoiced -->
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header" style="background-color: #e3f2fd; color: #000;">
                    <strong>Inventory invoiced</strong>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="inventory-invoiced-list">
                    <ul class="list-group" id="inventory-invoiced-jobcards-list">
                        {{-- Content here --}}
                    </ul>
                </div>
            </div>
        </div>
        <!-- Outstanding Jobcards -->
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header" style="background-color: #e3f2fd; color: #000;">
                    <strong>Outstanding jobcards</strong>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="outstanding-jobcards-list">
                    <ul class="list-group" id="outstanding-jobcards-jobcards-list">
                        {{-- Content here --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection