{{-- filepath: resources/views/progress_show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('progress') }}" class="btn btn-secondary mb-3">Back to Progress</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Jobcard: {{ $jobcard->jobcard_number }}</h2>
            <p><strong>Status:</strong> {{ $jobcard->status }}</p>
            <p><strong>Client:</strong> {{ $jobcard->client->name ?? '' }}</p>
            <p><strong>Work Done:</strong> {{ $jobcard->work_done }}</p>
            <h4>Employees & Hours Worked</h4>
            <ul>
                @php
                    $totalHours = 0;
                @endphp
                @foreach($jobcard->employees as $employee)
                    <li>
                        {{ $employee->name }} ({{ $employee->pivot->hours_worked ?? 0 }} hours)
                        @php $totalHours += $employee->pivot->hours_worked ?? 0; @endphp
                    </li>
                @endforeach
            </ul>
            <p><strong>Total Hours Worked:</strong> {{ $totalHours }} hours</p>
            <h4>Inventory Used</h4>
            <ul>
                @foreach($jobcard->inventory as $item)
                    <li>{{ $item->name }} (Qty: {{ $item->pivot->quantity }})</li>
                @endforeach
            </ul>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}" class="me-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="save">
                    <button type="submit" class="btn btn-primary">Save Progress</button>
                </form>
                <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}" class="me-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="completed">
                    <button type="submit" class="btn btn-success">Completed</button>
                </form>
                @if($jobcard->status === 'completed')
                    <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="invoice">
                        <button type="submit" class="btn btn-warning">Submit for Invoice</button>
                    </form>
                @else
                    <button type="button" class="btn btn-warning" disabled>Submit for Invoice</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
