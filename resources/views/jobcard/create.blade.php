@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Jobcard for {{ $client->name }}</h2>
    <form action="{{ route('jobcard.store') }}" method="POST">
        @csrf
        <input type="hidden" name="client_id" value="{{ $client->id }}">

        <div class="mb-3">
            <label>Client Name</label>
            <input type="text" value="{{ $client->name }}" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label>Telephone</label>
            <input type="text" value="{{ $client->telephone }}" class="form-control" readonly>
        </div>
        <!-- Add other client fields as needed -->

        <div class="mb-3">
            <label>Assign Employees</label>
            <select name="employees[]" class="form-control" multiple>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Work Done</label>
            <textarea name="work_done" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                @foreach($statuses as $status)
                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Jobcard</button>
    </form>
</div>
@endsection