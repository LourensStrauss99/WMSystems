@extends('layouts.mobile')

@section('header', 'Jobcard Details')

@section('content')
    <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1rem; margin-bottom: 1rem;">
        <div style="font-size: 1.1rem; font-weight: bold; margin-bottom: 0.5rem; color: #2563eb;">
            {{ $jobcard->jobcard_number }}
        </div>
        <div style="color: #64748b; margin-bottom: 0.3rem; font-weight: 600;">{{ $jobcard->client->name ?? '' }}</div>
        <div style="color: #64748b; margin-bottom: 0.3rem;">{{ $jobcard->client->address ?? '' }}</div>
        <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Date: {{ $jobcard->job_date }}</div>
        <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Status: <span style="color: #059669; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $jobcard->status)) }}</span></div>
        <div style="margin-bottom: 0.3rem;"><span style="font-weight: 500;">Work Request:</span> {{ $jobcard->work_request }}</div>
        <div style="margin-bottom: 0.3rem;"><span style="font-weight: 500; color: #dc2626;">Special Request:</span> {{ $jobcard->special_request }}</div>
        <div style="margin-top: 1rem;">
            <a href="{{ route('mobile.jobcards.edit', $jobcard->id) }}" style="background: #2563eb; color: #fff; border: none; border-radius: 4px; padding: 0.4rem 1rem; text-decoration: none; font-size: 0.95rem;">Edit</a>
        </div>
    </div>
@endsection