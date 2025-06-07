{{-- filepath: resources/views/progress_show.blade.php --}}
@extends('layouts.app')
@extends('layouts.nav')

@section('content')
<div class="container mt-4">
    <a href="{{ route('progress') }}" class="btn btn-secondary mb-3">Back to Progress</a>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-warning text-dark text-center py-3">
                    <h3 class="mb-0">Jobcard Progress - #{{ $jobcard->jobcard_number }}</h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('progress.jobcard.update', $jobcard->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Date</label>
                                <input type="text" value="{{ $jobcard->job_date }}" readonly class="form-control bg-light" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Client Name</label>
                                <input type="text" value="{{ $jobcard->client->name ?? '' }}" readonly class="form-control bg-light" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Inventory Used</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobcard->inventory as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                <input type="number" name="inventory[{{ $item->id }}]" class="form-control" min="0"
                                                    value="{{ $item->pivot->quantity }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Time Spent</label>
                            <select name="time_spent" class="form-control">
                                @for($i = 0; $i <= 8*4; $i++)
                                    @php $minutes = $i * 15; @endphp
                                    <option value="{{ $minutes }}" {{ $jobcard->time_spent == $minutes ? 'selected' : '' }}>
                                        {{ sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Work Done</label>
                            <textarea name="work_done" class="form-control" rows="2">{{ old('work_done', $jobcard->work_done) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Progress Note</label>
                            <textarea name="progress_note" class="form-control" rows="2">{{ old('progress_note', $jobcard->progress_note) }}</textarea>
                        </div>

                       
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" name="action" value="save" class="btn btn-primary">Save Progress</button>
                            <button type="submit" name="action" value="completed" class="btn btn-success">Completed</button>
                            @if($jobcard->status === 'completed')
                                <button type="submit" name="action" value="invoice" class="btn btn-warning">Submit for Invoice</button>
                            @else
                                <button type="button" class="btn btn-warning" disabled>Submit for Invoice</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection