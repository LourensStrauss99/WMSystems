<div class="container bg-white p-4 rounded shadow" style="max-width: 700px; margin: auto; font-family: 'Arial', sans-serif;">
    <h2 class="mb-3">New Quote</h2>
    <form wire:submit.prevent="saveQuote">
        <div class="mb-3">
            <label class="form-label">Client</label>
            <div class="d-flex align-items-center gap-2">
                <select wire:model="client_id" class="form-control" required>
                    <option value="">Select client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }} {{ $client->surname }} ({{ $client->email }})</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-primary" onclick="window.location='{{ url('/client/create') }}'">Add New Client</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Quote Number</label>
            <input type="text" class="form-control" value="{{ $quote_number }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Quote Date</label>
            <input type="date" class="form-control" wire:model="quote_date">
        </div>
        <div class="mb-3">
            <label class="form-label">Work Request</label>
            <input type="text" class="form-control" wire:model="work_request">
        </div>
        <div class="mb-3">
            <label class="form-label">Special Request</label>
            <input type="text" class="form-control" wire:model="special_request">
        </div>

        <div class="mb-3">
            <label class="form-label">Inventory Items</label>
            <div>
                @foreach($inventory_items as $index => $item)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <select wire:model="inventory_items.{{ $index }}.inventory_id" class="form-control" style="max-width: 250px;">
                            <option value="">Select item...</option>
                            @foreach($all_inventory as $inv)
                                <option value="{{ $inv->id }}">[{{ $inv->short_code }}] {{ $inv->description }} (Stock: {{ $inv->quantity }})</option>
                            @endforeach
                        </select>
                        <input type="number" min="1" wire:model="inventory_items.{{ $index }}.quantity" class="form-control" style="width: 80px;" placeholder="Qty">
                        <button type="button" class="btn btn-danger btn-sm" wire:click="removeInventoryItem({{ $index }})">Remove</button>
                    </div>
                @endforeach
                <button type="button" class="btn btn-success btn-sm" wire:click="addInventoryItem">Add Inventory Item</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Hours</label>
            <div>
                @foreach($hours as $index => $hour)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <select wire:model="hours.{{ $index }}.type" class="form-control" style="max-width: 150px;">
                            <option value="normal">Normal</option>
                            <option value="overtime">Overtime</option>
                            <option value="weekend">Weekend</option>
                            <option value="public_holiday">Public Holiday</option>
                        </select>
                        <input type="number" min="0" step="0.25" wire:model="hours.{{ $index }}.hours" class="form-control" style="width: 80px;" placeholder="Hours">
                        <button type="button" class="btn btn-danger btn-sm" wire:click="removeHourRow({{ $index }})">Remove</button>
                    </div>
                @endforeach
                <button type="button" class="btn btn-success btn-sm" wire:click="addHourRow">Add Hours</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Notes / Terms</label>
            <textarea class="form-control" wire:model="notes"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Quote</button>
        @if (session()->has('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
    </form>

    @if($showAddClientModal)
        <div class="modal fade show" style="display:block; background:rgba(0,0,0,0.4);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Client</h5>
                        <button type="button" class="btn-close" wire:click="hideAddClientModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Name</label>
                            <input type="text" class="form-control" wire:model="new_client.name">
                        </div>
                        <div class="mb-2">
                            <label>Surname</label>
                            <input type="text" class="form-control" wire:model="new_client.surname">
                        </div>
                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" class="form-control" wire:model="new_client.email">
                        </div>
                        <div class="mb-2">
                            <label>Telephone</label>
                            <input type="text" class="form-control" wire:model="new_client.telephone">
                        </div>
                        <div class="mb-2">
                            <label>Address</label>
                            <input type="text" class="form-control" wire:model="new_client.address">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideAddClientModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="addClient">Add Client</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
