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
            <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}" class="mb-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="save">
                <label for="work_done" class="form-label"><strong>Work Done:</strong></label>
                <textarea name="work_done" id="work_done" class="form-control mb-2" rows="2" placeholder="Describe work done...">{{ old('work_done', $jobcard->work_done) }}</textarea>
                <button type="submit" class="btn btn-primary">Save Progress</button>
            </form>
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
                    <li>
                        {{ $item->name ?? $item->description ?? 'Item' }} 
                        (Qty: {{ $item->pivot->quantity ?? 0 }})
                        @if($item->selling_price || $item->sell_price)
                            - R{{ number_format(($item->pivot->quantity ?? 0) * ($item->selling_price ?? $item->sell_price ?? 0), 2) }}
                        @endif
                    </li>
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
                <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}">
                    @csrf
                    @method('PUT')
                    <button type="submit" name="action" value="invoice" class="btn btn-primary">
                        Submit for Invoice
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
