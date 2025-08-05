@extends('layouts.mobile')

@section('header', 'Edit Jobcard')

@section('content')
<style>
    body, .mobile-content {
        background: #f3f4f6 !important;
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 1.08rem;
    }
    .mobile-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 1.2rem 1rem 1rem 1rem;
        margin-bottom: 1.2rem;
    }
    .mobile-section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 0.7rem;
        letter-spacing: 0.01em;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.2rem;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 1rem;
        margin-bottom: 0.7rem;
        padding: 0.6rem 0.9rem;
    }
    textarea.form-control {
        min-height: 2.2rem;
    }
    .btn-primary, .btn-success {
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.05rem;
        padding: 0.7rem 0;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    .btn-link {
        color: #2563eb;
        text-align: center;
        display: block;
        margin-top: 0.5rem;
        font-size: 1rem;
    }
    .list-group-item {
        border: none;
        border-radius: 8px;
        background: #f9fafb;
        margin-bottom: 0.4rem;
        font-size: 0.98rem;
    }
    .badge {
        font-size: 0.92em;
        border-radius: 6px;
        padding: 0.2em 0.6em;
    }
    .mobile-btn-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
</style>
<div style="padding: 0.5rem;">
    <div class="mobile-btn-row">
        <a href="{{ route('jobcard.pdf', $jobcard->id) }}" target="_blank" class="btn btn-primary">Export PDF</a>
        <a href="{{ route('mobile.jobcards.index') }}" class="btn btn-link" style="background: #f3f4f6; border: 1px solid #2563eb;">Back to List</a>
    </div>
    <form method="POST" action="{{ route('jobcard.update', $jobcard->id) }}">
        @csrf
        @method('PUT')
        <!-- Basic Information Card -->
        <div class="mobile-card">
            <div class="mobile-section-title">Basic Information</div>
            <label class="form-label">Jobcard Number</label>
            <input type="text" name="jobcard_number" class="form-control" value="{{ old('jobcard_number', $jobcard->jobcard_number) }}" required>
            <label class="form-label">Job Date</label>
            <input type="date" name="job_date" class="form-control" value="{{ old('job_date', $jobcard->job_date) }}" required>
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control" required>@foreach($clients as $client)<option value="{{ $client->id }}" {{ $jobcard->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>@endforeach</select>
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="{{ old('category', $jobcard->category) }}">
            <label class="form-label">Work Request</label>
            <textarea name="work_request" class="form-control">{{ old('work_request', $jobcard->work_request) }}</textarea>
            <label class="form-label">Special Request</label>
            <textarea name="special_request" class="form-control">{{ old('special_request', $jobcard->special_request) }}</textarea>
            <label class="form-label">Work Done</label>
            <textarea name="work_done" class="form-control">{{ old('work_done', $jobcard->work_done) }}</textarea>
        </div>
        <!-- Employees Card -->
        <div class="mobile-card">
            <div class="mobile-section-title">Assigned Employees & Hours</div>
            <select id="employee_select" class="form-control mb-1"><option value="">Select Employee</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->name }}</option>@endforeach</select>
            <input type="number" id="employee_hours_input" class="form-control mb-1" min="0" step="0.5" placeholder="Hours">
            <select id="hour_type_select" class="form-control mb-1"><option value="normal">Normal</option><option value="overtime">Overtime</option><option value="weekend">Weekend</option><option value="holiday">Holiday</option></select>
            <button type="button" class="btn btn-success" onclick="addEmployee()">Add Employee</button>
            <ul id="employee_list" class="list-group">
                @forelse($jobcard->employees as $employee)
                    @php $hourType = $employee->pivot->hour_type ?? 'normal'; $hours = $employee->pivot->hours_worked ?? 0; $company = \App\Models\CompanyDetail::first(); $baseRate = $company->labour_rate ?? 750; switch($hourType) { case 'overtime': $rate = $baseRate * ($company->overtime_multiplier ?? 1.5); $label = 'Overtime'; break; case 'weekend': $rate = $baseRate * ($company->weekend_multiplier ?? 2.0); $label = 'Weekend'; break; case 'holiday': $rate = $baseRate * ($company->public_holiday_multiplier ?? 2.5); $label = 'Holiday'; break; default: $rate = $baseRate; $label = 'Normal'; } $cost = $hours * $rate; @endphp
                    <li data-id="{{ $employee->id }}" data-hours="{{ $hours }}" data-type="{{ $hourType }}" class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>{{ $employee->name }}</strong> <span class="badge bg-info ms-2">{{ $hours }} hrs</span> <span class="badge bg-secondary ms-1">{{ $label }}</span> <small class="text-muted ms-2">R{{ number_format($cost, 2) }}</small></span>
                        <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                        <input type="hidden" name="employee_hours[{{ $employee->id }}]" value="{{ $hours }}">
                        <input type="hidden" name="employee_hour_types[{{ $employee->id }}]" value="{{ $hourType }}">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee(this)">Remove</button>
                    </li>
                @empty
                    <li class="text-muted list-group-item">No employees assigned yet</li>
                @endforelse
            </ul>
        </div>
        <!-- Inventory Card -->
        <div class="mobile-card">
            <div class="mobile-section-title">Inventory Items</div>
            <select id="inventory_select" class="form-control mb-2"><option value="">Select Inventory Item</option>@foreach($inventory as $item)<option value="{{ $item->id }}">[{{ $item->short_code }}] {{ $item->name }} (Stock: {{ $item->stock_level }})</option>@endforeach</select>
            <input type="number" id="inventory_quantity" class="form-control mb-2" min="1" max="100" value="1" placeholder="Qty">
            <button type="button" id="add_inventory" class="btn btn-primary">Add Item</button>
            <ul id="inventory_list" class="list-group">
                @forelse($assignedInventory as $item)
                    <li data-id="{{ $item['id'] }}" class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>{{ $item['name'] }}</strong> <span class="badge bg-warning text-dark ms-2">Qty: {{ $item['quantity'] }}</span></span>
                        <input type="hidden" name="inventory_items[]" value="{{ $item['id'] }}">
                        <input type="hidden" name="inventory_qty[{{ $item['id'] }}]" value="{{ $item['quantity'] }}">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInventory(this)">Remove</button>
                    </li>
                @empty
                    <li class="text-muted list-group-item">No inventory items added yet</li>
                @endforelse
            </ul>
        </div>
        <!-- Status and Hours Card -->
        <div class="mobile-card">
            <div class="mobile-section-title">Status & Hours</div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required><option value="assigned" {{ $jobcard->status == 'assigned' ? 'selected' : '' }}>Assigned</option><option value="in progress" {{ $jobcard->status == 'in progress' ? 'selected' : '' }}>In Progress</option><option value="completed" {{ $jobcard->status == 'completed' ? 'selected' : '' }}>Completed</option><option value="invoiced" {{ $jobcard->status == 'invoiced' ? 'selected' : '' }}>Invoiced</option></select>
            <label class="form-label">Call Out Fee</label>
            <input type="number" name="call_out_fee" class="form-control" step="0.01" value="{{ old('call_out_fee', $jobcard->call_out_fee) }}">
            <label class="form-label">Mileage (km)</label>
            <input type="number" name="mileage_km" class="form-control" step="0.1" value="{{ old('mileage_km', $jobcard->mileage_km) }}">
            <label class="form-label">Mileage Cost</label>
            <input type="number" name="mileage_cost" class="form-control" step="0.01" value="{{ old('mileage_cost', $jobcard->mileage_cost) }}">
            <label class="form-label">Normal Hours</label>
            <input type="number" name="normal_hours" class="form-control" step="0.01" value="{{ old('normal_hours', $jobcard->normal_hours) }}">
            <label class="form-label">Overtime Hours</label>
            <input type="number" name="overtime_hours" class="form-control" step="0.01" value="{{ old('overtime_hours', $jobcard->overtime_hours) }}">
            <label class="form-label">Weekend Hours</label>
            <input type="number" name="weekend_hours" class="form-control" step="0.01" value="{{ old('weekend_hours', $jobcard->weekend_hours) }}">
            <label class="form-label">Public Holiday Hours</label>
            <input type="number" name="public_holiday_hours" class="form-control" step="0.01" value="{{ old('public_holiday_hours', $jobcard->public_holiday_hours) }}">
            <label class="form-label">Total Labour Cost</label>
            <input type="number" name="total_labour_cost" class="form-control" step="0.01" value="{{ old('total_labour_cost', $jobcard->total_labour_cost) }}">
        </div>
        @if(!$jobcard->quote_accepted_at)
            <div class="mb-3">
                <label class="form-label fw-bold text-muted">
                    <input type="checkbox" name="is_quote" value="1" {{ old('is_quote', $jobcard->is_quote) ? 'checked' : '' }}> This is a quote
                </label>
            </div>
        @endif
        @if($jobcard->is_quote && !$jobcard->quote_accepted_at)
            <div class="alert alert-info">
                <form method="POST" action="{{ route('jobcard.acceptQuote', $jobcard->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label for="accepted_signature" class="form-label">Signature (type your name to accept):</label>
                        <input type="text" name="accepted_signature" id="accepted_signature" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Accept Quote</button>
                </form>
            </div>
        @elseif($jobcard->quote_accepted_at)
            <div class="alert alert-success">
                <strong>Quote Accepted & Converted to Jobcard</strong><br>
                Accepted by user ID: {{ $jobcard->accepted_by }} at {{ $jobcard->quote_accepted_at->format('Y-m-d H:i:s') }}<br>
                Signature: {{ $jobcard->accepted_signature }}
            </div>
        @endif
        <button type="submit" class="btn btn-primary">Update Jobcard</button>
    </form>
</div>
<script>
// AddEmployee, RemoveEmployee, AddInventory, RemoveInventory JS logic can be copied/adapted from the main editor as needed
</script>
@endsection 