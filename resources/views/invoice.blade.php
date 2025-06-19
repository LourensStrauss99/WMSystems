@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Submitted Invoices</h2>
    <form method="GET" action="{{ route('invoice.index') }}" class="mb-3 row g-2 align-items-end">
        <div class="col">
            <input type="text" name="client" class="form-control" placeholder="Search by client name" value="{{ request('client') }}">
        </div>
        <div class="col">
            <label for="from" class="form-label mb-0">From</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col">
            <label for="to" class="form-label mb-0">To</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
    <div style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobcards as $jobcard)
                    <tr>
                        <td>{{ $jobcard->id }}</td>
                        <td>{{ $jobcard->client->name ?? '-' }}</td>
                        <td>{{ $jobcard->updated_at->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($jobcard->status) }}</td>
                        <td>
                            <a href="{{ route('invoice.show', $jobcard->id) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $jobcards->links() }}
    </div>
</div>
@endsection