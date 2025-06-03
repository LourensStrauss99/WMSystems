@extends('layouts.app')
@extends('layouts.nav')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Submitted Invoices</h2>
    <form method="GET" action="{{ route('invoice.index') }}" class="mb-3">
        <input type="text" name="client" class="form-control" placeholder="Search by client name" value="{{ request('client') }}">
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>
    <div style="max-height: 400px; overflow-y: auto;">
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
                    {{-- display jobcard info --}}
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<style>
.invoice-row { cursor: pointer; }
</style>
@endsection