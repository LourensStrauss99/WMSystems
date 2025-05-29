@extends('layouts.app')
@extends('layouts.nav')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Submitted Invoices</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Client</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobcards as $jobcard)
                <tr class="invoice-row" ondblclick="window.location='{{ route('invoices.show', $jobcard->id) }}'">
                    <td>{{ $jobcard->jobcard_number }}</td>
                    <td>{{ $jobcard->client->name }}</td>
                    <td>{{ $jobcard->job_date }}</td>
                    <td>{{ $jobcard->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<style>
.invoice-row { cursor: pointer; }
</style>
@endsection