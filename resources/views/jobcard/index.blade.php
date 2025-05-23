@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Jobcard Search</h2>
    <form method="GET" action="{{ route('jobcard.index') }}" class="mb-3">
        <input type="text" name="client" placeholder="Search by client name" value="{{ request('client') }}" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    @if($jobcards)
        <table class="table">
            <thead>
                <tr>
                    <th>Jobcard #</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobcards as $jobcard)
                    <tr>
                        <td>{{ $jobcard->jobcard_number }}</td>
                        <td>{{ $jobcard->client->name }}</td>
                        <td>{{ $jobcard->job_date }}</td>
                        <td>
                            <a href="{{ route('jobcard.show', $jobcard->id) }}" class="btn btn-sm btn-info">View/Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No jobcards found.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <hr>
    <h3>Create New Jobcard</h3>
    {{-- Place your empty jobcard form here --}}
    @include('jobcard.create-form')
</div>
@endsection