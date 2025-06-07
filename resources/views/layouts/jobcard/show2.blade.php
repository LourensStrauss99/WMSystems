<!-- filepath: resources/views/jobcard/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Job Card Details</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jobcard Number</label>
                            <input type="text" value="{{ $jobcard->jobcard_number }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Date</label>
                            <input type="date" value="{{ $jobcard->job_date }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <input type="text" value="{{ $jobcard->client->name }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Surname</label>
                            <input type="text" value="{{ $jobcard->client->surname }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telephone</label>
                            <input type="text" value="{{ $jobcard->client->telephone }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" value="{{ $jobcard->client->address }}" readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" value="{{ $jobcard->client->email }}" readonly class="form-control bg-light" />
                        </div>
                    </div>

                    <form method="POST" action="{{ route('jobcard.update', $jobcard->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Assign Employees Section -->
                        <div class="mb-3">
                            <label class="form-label">Assign Employees</label>
                            <div class="input-group mb-2">
                                <select id="employee_select" class="form-control select2">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="add_employee" class="btn btn-primary">Add</button>
                            </div>
                            <ul id="employee_list" class="list-group mb-3"></ul>
                            <input type="hidden" name="employees" id="employees_data">
                        </div>

                        <!-- Inventory Section -->
                        <div class="mb-3">
                            <label class="form-label">Inventory Used</label>
                            <div class="mb-2 position-relative">
                                <input type="text" id="inventory_search" class="form-control" placeholder="Search inventory..." autocomplete="off">
                                <ul id="inventory_suggestions" class="list-group position-absolute w-100" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></ul>
                            </div>
                            <div class="input-group mb-3">
                                <select id="inventory_select" class="form-control">
                                    <option value="">Select Inventory</option>
                                    @foreach($inventory as $item)
                                        <option value="{{ $item->id }}" data-short="{{ $item->short_description }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <select id="inventory_quantity" class="form-control">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <button type="button" id="add_inventory" class="btn btn-primary">Add</button>
                            </div>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory_list"></tbody>
                            </table>
                            <input type="hidden" name="inventory_data" id="inventory_data">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Time Spent</label>
                            <select name="time_spent" class="form-control">
                                @for($i = 0; $i <= 8*4; $i++)
                                    @php $minutes = $i * 15; @endphp
                                    <option value="{{ $minutes }}" 
                                        {{ $jobcard->time_spent == $minutes ? 'selected' : '' }}>
                                        {{ sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Work Done</label>
                            <textarea name="work_done" class="form-control" rows="3">{{ $jobcard->work_done }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="assigned" {{ $jobcard->status == 'assigned' ? 'selected' : '' }}>
                                    Assigned
                                </option>
                                <option value="in progress" {{ $jobcard->status == 'in progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ $jobcard->status == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('jobcard.index') }}" class="btn btn-secondary">
                                ← Back to Search
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Save Jobcard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    // Initial data from backend
    let employeeList = @json($jobcard->employees->map(fn($e) => ['id'=>$e->id, 'name'=>$e->name])->values());
    let inventoryList = @json($jobcard->inventory->map(fn($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'quantity' => (int) $item->pivot->quantity
    ])->values());
    const allEmployees = @json($employees->map(fn($e) => ['id'=>$e->id, 'name'=>$e->name])->values());
    const inventoryOptions = @json($inventory->map(fn($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'short' => $item->short_description
    ])->values());

    // --- Employees ---
    function renderEmployeeList() {
        const ul = $('#employee_list');
        ul.empty();
        employeeList.forEach(emp => {
            ul.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${emp.name}
                    <button type="button" class="btn btn-danger btn-sm remove-employee" data-id="${emp.id}">Remove</button>
                </li>
            `);
        });
        $('#employees_data').val(JSON.stringify(employeeList.map(e => e.id)));
    }

    $('#add_employee').click(function() {
        const id = $('#employee_select').val();
        const name = $('#employee_select option:selected').text();
        if (!id || employeeList.find(e => e.id == id)) return;
        employeeList.push({id: parseInt(id), name});
        renderEmployeeList();
        $('#employee_select').val('').trigger('change');
    });

    $(document).on('click', '.remove-employee', function() {
        const id = $(this).data('id');
        employeeList = employeeList.filter(emp => emp.id != id);
        renderEmployeeList();
    });

    // --- Inventory ---
    function renderInventoryList() {
        const tbody = $('#inventory_list');
        tbody.empty();
        inventoryList.forEach(item => {
            tbody.append(`
                <tr data-id="${item.id}">
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm edit-inventory">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm remove-inventory">Remove</button>
                    </td>
                </tr>
            `);
        });
        $('#inventory_data').val(JSON.stringify(inventoryList));
    }

    $('#add_inventory').click(function() {
        const id = $('#inventory_select').val();
        const name = $('#inventory_select option:selected').text();
        const quantity = parseInt($('#inventory_quantity').val());
        if (!id || !quantity || quantity < 1) return;
        let existingItem = inventoryList.find(item => item.id == id);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            inventoryList.push({id: parseInt(id), name, quantity});
        }
        renderInventoryList();
        $('#inventory_select').val('').trigger('change');
        $('#inventory_quantity').val('1');
        $('#inventory_search').val('');
        $('#inventory_suggestions').hide();
    });

    $(document).on('click', '.remove-inventory', function() {
        const id = $(this).closest('tr').data('id');
        inventoryList = inventoryList.filter(item => item.id != id);
        renderInventoryList();
    });

    $(document).on('click', '.edit-inventory', function() {
        const tr = $(this).closest('tr');
        const id = tr.data('id');
        const item = inventoryList.find(item => item.id == id);
        $('#inventory_select').val(id);
        $('#inventory_quantity').val(item.quantity);
        inventoryList = inventoryList.filter(item => item.id != id);
        renderInventoryList();
    });

    // --- Inventory Search ---
    $('#inventory_search').on('input', function() {
        const search = $(this).val().toLowerCase();
        const suggestions = $('#inventory_suggestions');
        suggestions.empty();
        if (!search) {
            suggestions.hide();
            return;
        }
        const filtered = inventoryOptions.filter(item =>
            item.name.toLowerCase().includes(search) ||
            (item.short || '').toLowerCase().includes(search)
        );
        filtered.forEach(item => {
            suggestions.append(`
                <li class="list-group-item list-group-item-action" data-id="${item.id}">
                    ${item.name}
                </li>
            `);
        });
        suggestions.show();
    });

    $(document).on('click', '#inventory_suggestions li', function() {
        const id = $(this).data('id');
        $('#inventory_select').val(id);
        $('#inventory_search').val($(this).text());
        $('#inventory_suggestions').hide();
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#inventory_search, #inventory_suggestions').length) {
            $('#inventory_suggestions').hide();
        }
    });

    // Initial render
    renderEmployeeList();
    renderInventoryList();
});
</script>
@endpush