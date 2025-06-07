{{-- filepath: resources/views/livewire/jobcard-editor.blade.php --}}



<div>
    {{-- Readonly Jobcard Info --}}
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

    {{-- Livewire Editing Form --}}
    <form wire:submit.prevent="save">
        <!-- Employees -->
        <div class="mb-3">
            <label>Assign Employees</label>
            <select wire:model="selectedEmployee" class="form-control">
                <option value="">Select Employee</option>
                @if(isset($employees))
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                @endif
            </select>
            <button type="button" wire:click="addEmployee" class="btn btn-primary mt-2">Add</button>
            <ul class="list-group mt-2">
                @if(isset($employees))
                    @foreach($assignedEmployees as $empId)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ optional($employees->firstWhere('id', $empId))->name }}
                            <button type="button" wire:click="removeEmployee({{ $empId }})" class="btn btn-danger btn-sm">Remove</button>
                        </li>
                    @endforeach  
                @endif
            </ul>
        </div>

        <!-- Inventory -->
        <div class="mb-3">
            <label>Inventory</label>
            <div class="input-group mb-2">
                <select wire:model="selectedInventory" class="form-control">
                    <option value="">Select Inventory</option>
                    @if(isset($inventory))
                        @foreach($inventory as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    @endif
                </select>
                <input type="number" wire:model="inventoryQuantity" class="form-control" min="1" value="1">
                <button type="button" wire:click="addInventory" class="btn btn-primary">Add</button>
            </div>
            <ul class="list-group mt-2">
                @foreach($assignedInventory as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ optional($inventory->firstWhere('id', $item['id']))->name ?? 'Unknown' }} (Qty: {{ $item['quantity'] }})
                        <span class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" wire:click.prevent="editInventory({{ $loop->index }})">Edit</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" wire:click.prevent="removeInventory({{ $loop->index }})">Remove</a>
                                </li>
                            </ul>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Other fields -->
        <div class="mb-3">
            <label>Status</label>
            <select wire:model="status" class="form-control">
                <option value="assigned">Assigned</option>
                <option value="in progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Work Done</label>
            <textarea wire:model="work_done" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Time Spent</label>
            <div class="d-flex gap-2">
                <select wire:model="hours" class="form-control" style="max-width: 120px;">
                    @for($h = 0; $h <= 100; $h++)
                        <option value="{{ $h }}">{{ $h }} h</option>
                    @endfor
                </select>
                <select wire:model="minutes" class="form-control" style="max-width: 120px;">
                    @foreach([0, 15, 30, 45] as $m)
                        <option value="{{ $m }}">{{ $m }} m</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Jobcard</button>
    </form>
    @if(session()->has('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    
</div>



