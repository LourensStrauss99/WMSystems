{{-- filepath: resources/views/progress_show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('progress') }}" class="btn btn-outline-primary mb-4">
        <i class="fas fa-arrow-left me-2"></i>Back to Progress
    </a>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-3">
                        <i class="fas fa-clipboard-list text-primary me-2"></i>Jobcard: <span class="text-dark">{{ $jobcard->jobcard_number }}</span>
                    </h3>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <span class="fw-bold text-muted">Status:</span>
                            <span class="badge 
                                @if($jobcard->status === 'in progress') bg-info text-dark
                                @elseif($jobcard->status === 'completed') bg-success
                                @elseif($jobcard->status === 'assigned') bg-secondary
                                @elseif($jobcard->status === 'invoiced') bg-warning text-dark
                                @else bg-light text-dark @endif">
                                {{ ucfirst($jobcard->status) }}
                            </span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="fw-bold text-muted">Client:</span>
                            <span class="text-dark">{{ $jobcard->client->name ?? '' }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}" class="mb-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="save">
                        <label for="work_done" class="form-label fw-bold text-muted">
                            <i class="fas fa-tasks me-1"></i>Work Done:
                        </label>
                        <textarea name="work_done" id="work_done" class="form-control mb-3" rows="2" placeholder="Describe work done...">{{ old('work_done', $jobcard->work_done) }}</textarea>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Progress
                        </button>
                    </form>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-users text-success me-2"></i>Employees & Hours Worked
                        </h5>
                        <ul class="list-group mb-2">
                            @php $totalHours = 0; @endphp
                            @foreach($jobcard->employees as $employee)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong>{{ $employee->name }}</strong>
                                        @if(($employee->pivot->hour_type ?? 'normal') === 'traveling')
                                            <span class="badge bg-info text-dark ms-2"><i class="fas fa-car-side me-1"></i>Traveling: {{ $employee->pivot->travel_km ?? 0 }} km</span>
                                        @else
                                            <span class="badge bg-primary ms-2">{{ $employee->pivot->hours_worked ?? 0 }} hrs</span>
                                            @if(!empty($employee->pivot->hour_type) && $employee->pivot->hour_type !== 'normal')
                                                <span class="badge bg-secondary ms-1">{{ ucfirst(str_replace('_', ' ', $employee->pivot->hour_type)) }}</span>
                                            @endif
                                            @php $totalHours += $employee->pivot->hours_worked ?? 0; @endphp
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="text-end mt-2">
                            <span class="fw-bold text-muted">Total Hours Worked:</span>
                            <span class="fw-bold text-success">{{ $totalHours }} hours</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-boxes text-warning me-2"></i>Inventory Used
                        </h5>
                        <ul class="list-group">
                            @foreach($jobcard->inventory as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $item->name ?? $item->description ?? 'Item' }}</span>
                                    <span class="badge bg-warning text-dark ms-2">Qty: {{ $item->pivot->quantity ?? 0 }}</span>
                                    @if($item->selling_price || $item->sell_price)
                                        <span class="text-muted ms-2">R{{ number_format(($item->pivot->quantity ?? 0) * ($item->selling_price ?? $item->sell_price ?? 0), 2) }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-end mt-3">
                        <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="save">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-save me-1"></i>Save Progress
                            </button>
                        </form>
                        <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="completed">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i>Completed
                            </button>
                        </form>
                        <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" name="action" value="invoice" class="btn btn-warning text-dark">
                                <i class="fas fa-file-invoice me-1"></i>Submit for Invoice
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
