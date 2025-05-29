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
                            <input type="text" value="{{ $jobcard->jobcard_number }}" 
                                   readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Date</label>
                            <input type="date" value="{{ $jobcard->job_date }}" 
                                   readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <input type="text" value="{{ $jobcard->client->name }}" 
                                   readonly class="form-control bg-light" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Surname</label>
                            <input type="text" value="{{ $jobcard->client->surname }}" 
                                   readonly class="form-control bg-light" />
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

                        <div class="mb-3">
                            <label class="form-label">Assign Employees</label>
                            <select name="employees[]" multiple class="form-control select2">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                        {{ in_array($employee->id, $jobcard->employees->pluck('id')->toArray() ?? []) ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Inventory section -->
                        <div class="mb-3">
                            <label class="form-label">Inventory Used</label>
                            <div class="input-group mb-3">
                                <select id="inventory_select" class="form-control">
                                    <option value="">Select Inventory</option>
                                    @foreach($inventory as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" id="inventory_quantity" 
                                       class="form-control" min="1" value="1">
                                <button type="button" id="add_inventory" 
                                        class="btn btn-primary">Add</button>
                            </div>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory_list">
                                    @foreach($jobcard->inventory as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->pivot->quantity }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <input type="hidden" name="inventory_data" id="inventory_data">

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
                            <textarea name="work_done" class="form-control" 
                                      rows="3">{{ $jobcard->work_done }}</textarea>
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
        
        // Initialize inventory list with type declaration
        /** @type {Array<{id: number, name: string, quantity: number}>} */
        const inventoryList = @json($jobcard->inventory->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => (int)$item->pivot->quantity
            ];
        })->values());

        // Add inventory item
        $('#add_inventory').click(function() {
            const select = $('#inventory_select');
            const quantity = $('#inventory_quantity').val();
            const id = select.val();
            const name = select.find('option:selected').text();

            if (!id || !quantity || quantity < 1) return;

            // Check for duplicates and update quantity if exists
            const existingItem = inventoryList.find(item => item.id == id);
            if (existingItem) {
                existingItem.quantity = parseInt(existingItem.quantity) + parseInt(quantity);
            } else {
                inventoryList.push({id, name, quantity: parseInt(quantity)});
            }
            renderInventoryList();
        });

        function renderInventoryList() {
            const tbody = $('#inventory_list');
            tbody.empty();
            
            inventoryList.forEach((item, idx) => {
                tbody.append(`
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.quantity}</td>
                        <td>
                            <button type="button" 
                                    class="btn btn-danger btn-sm"
                                    onclick="removeInventory(${idx})">
                                Remove
                            </button>
                        </td>
                    </tr>
                `);
            });
            
            $('#inventory_data').val(JSON.stringify(inventoryList));
            
            // Reset inputs
            $('#inventory_select').val('');
            $('#inventory_quantity').val('1');
        }

        // Make removeInventory available globally
        window.removeInventory = function(idx) {
            inventoryList.splice(idx, 1);
            renderInventoryList();
        };

        // Initial render
        renderInventoryList();
    });
</script>
@endpush