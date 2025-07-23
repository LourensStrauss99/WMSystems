@extends('layouts.mobile')

@section('header', 'Jobcards')

@section('content')
    <h2 class="fw-bold mb-3">Jobcards</h2>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @foreach($jobcards as $jobcard)
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1rem; display: flex; flex-direction: column; align-items: flex-start;">
                <div style="font-size: 1.1rem; font-weight: bold; margin-bottom: 0.5rem; color: #2563eb;">
                    {{ $jobcard->jobcard_number }}
                </div>
                <div style="color: #64748b; margin-bottom: 0.3rem;">{{ $jobcard->client->name ?? '' }}</div>
                <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Date: {{ $jobcard->job_date }}</div>
                <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Status: <span style="color: #059669; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $jobcard->status)) }}</span></div>
                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                    <a href="{{ route('mobile.jobcards.edit', $jobcard->id) }}" style="background: #2563eb; color: #fff; border: none; border-radius: 4px; padding: 0.4rem 1rem; text-decoration: none; font-size: 0.95rem;">Edit</a>
                    <a href="{{ route('mobile.jobcards.show', $jobcard->id) }}" style="background: #f3f4f6; color: #2563eb; border: 1px solid #2563eb; border-radius: 4px; padding: 0.4rem 1rem; text-decoration: none; font-size: 0.95rem;">View</a>
                </div>
            </div>
        @endforeach
    </div>
    <div style="margin-top: 1rem;">
        {{ $jobcards->links() }}
    </div>
@endsection 