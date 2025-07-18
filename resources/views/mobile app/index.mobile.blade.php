@extends('layouts.mobile')

@section('content')
<div class="container-fluid px-2 py-2">
    <h4 class="mb-3">My Jobcards</h4>
    <div id="jobcard-list">
        @forelse($jobcards as $jobcard)
            <a href="{{ route('mobile.jobcard.edit', $jobcard->id) }}" class="card mb-2 shadow-sm text-decoration-none">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong>#{{ $jobcard->jobcard_number }}</strong>
                        <div class="small text-muted">{{ $jobcard->client->name ?? '' }}</div>
                        <div class="small">{{ ucfirst($jobcard->status) }}</div>
                    </div>
                    <span class="badge bg-primary">{{ $jobcard->created_at->format('d M') }}</span>
                </div>
            </a>
        @empty
            <div class="alert alert-info">No jobcards assigned.</div>
        @endforelse
    </div>
</div>
@endsection