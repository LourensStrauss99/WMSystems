{{-- filepath: resources/views/livewire/jobcard-editor.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Edit Jobcard</h2>
    <form method="POST" action="{{ route('jobcard.update', $jobcard->id) }}">
        @csrf
        @method('PUT')

        <!-- Basic Jobcard Fields -->
        <div class="mb-3">
            <label class="form-label">Jobcard Number</label>
            <input type="text" name="jobcard_number" class="form-control" value="{{ old('jobcard_number', $jobcard->jobcard_number) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Job Date</label>
            <input type="date" name="job_date" class="form-control" value="{{ old('job_date', $jobcard->job_date) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $jobcard->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="{{ old('category', $jobcard->category) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Work Request</label>
            <textarea name="work_request" class="form-control">{{ old('work_request', $jobcard->work_request) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Special Request</label>
            <textarea name="special_request" class="form-control">{{ old('special_request', $jobcard->special_request) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="assigned" {{ $jobcard->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in progress" {{ $jobcard->status == 'in progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $jobcard->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="invoiced" {{ $jobcard->status == 'invoiced' ? 'selected' : '' }}>Invoiced</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Work Done</label>
            <textarea name="work_done" class="form-control">{{ old('work_done', $jobcard->work_done) }}</textarea>
        </div>

       

        <!-- Employees Section -->
        <div class="mb-3">
            <label class="form-label">Add Employee</label>
            <div class="row g-2">
                <div class="col-md-5">
                    <select id="employee_select" class="form-control">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" id="employee_hours_input" class="form-control" min="0" placeholder="Hours Worked">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" onclick="addEmployee()">Add</button>
                </div>
            </div>
            <ul id="employee_list" class="mt-2">
                @foreach($jobcard->employees as $employee)
                    <li data-id="{{ $employee->id }}">
                        {{ $employee->name }} ({{ $employee->pivot->hours_worked ?? 0 }} hours)
                        <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                        <input type="hidden" name="employee_hours[{{ $employee->id }}]" value="{{ $employee->pivot->hours_worked ?? 0 }}">
                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeEmployee(this)">Remove</button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Inventory Section -->
        <div class="mb-3">
            <label class="form-label">Add Inventory Item</label>
            <div class="row g-2">
                <div class="col-md-5">
                    <select id="inventory_select" class="form-control">
                        <option value="">Select Item</option>
                        @foreach($inventory as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" id="inventory_qty_input" class="form-control" min="1" placeholder="Quantity">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" onclick="addInventory()">Add</button>
                </div>
            </div>
            <ul id="inventory_list" class="mt-2">
                @foreach($jobcard->inventory as $item)
                    <li data-id="{{ $item->id }}">
                        {{ $item->name }} (Qty: {{ $item->pivot->quantity ?? 0 }})
                        <input type="hidden" name="inventory_items[]" value="{{ $item->id }}">
                        <input type="hidden" name="inventory_qty[{{ $item->id }}]" value="{{ $item->pivot->quantity ?? 0 }}">
                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeInventory(this)">Remove</button>
                    </li>
                @endforeach
            </ul>
        </div>

        <button type="submit" class="btn btn-primary">Save Jobcard</button>
    </form>
</div>

<script>
function addEmployee() {
    let select = document.getElementById('employee_select');
    let hours = document.getElementById('employee_hours_input').value;
    let id = select.value;
    let name = select.options[select.selectedIndex].text;

    if (!id || !hours || hours < 0) return;

    // Prevent duplicate
    if (document.querySelector('#employee_list li[data-id="'+id+'"]')) return;

    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.innerHTML = `${name} (${hours} hours)
        <input type="hidden" name="employees[]" value="${id}">
        <input type="hidden" name="employee_hours[${id}]" value="${hours}">
        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeEmployee(this)">Remove</button>`;
    document.getElementById('employee_list').appendChild(li);
}

function removeEmployee(btn) {
    btn.parentElement.remove();
}

function addInventory() {
    let select = document.getElementById('inventory_select');
    let qty = document.getElementById('inventory_qty_input').value;
    let id = select.value;
    let name = select.options[select.selectedIndex].text;

    if (!id || !qty || qty < 1) return;

    // Prevent duplicate
    if (document.querySelector('#inventory_list li[data-id="'+id+'"]')) return;

    let li = document.createElement('li');
    li.setAttribute('data-id', id);
    li.innerHTML = `${name} (Qty: ${qty})
        <input type="hidden" name="inventory_items[]" value="${id}">
        <input type="hidden" name="inventory_qty[${id}]" value="${qty}">
        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeInventory(this)">Remove</button>`;
    document.getElementById('inventory_list').appendChild(li);
}

function removeInventory(btn) {
    btn.parentElement.remove();
}
</script>
@endsection



