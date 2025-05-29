<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Create Job Card</h3>
                </div>
                <div class="card-body p-4">
                    <form wire:submit.prevent="submit">
                        <div class="mb-3">
                            <label class="form-label">Jobcard Number</label>
                            <input type="text" wire:model="jobcard_number" readonly 
                                   class="form-control bg-light" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Date</label>
                            <input type="date" wire:model="job_date" required 
                                   class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Test</label>
                            <input type="text" wire:model="test" required class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <select wire:model="customer" required class="form-control">
                                <option value="">Select a client</option>
                                 @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }}
                                    </option>
                                @endforeach 
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Work Request</label>
                            <textarea wire:model="work_request" rows="3" 
                                      class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Instructions</label>
                            <textarea wire:model="special_request" rows="2" 
                                      class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Add Inventory Items</label>
                            <div class="input-group mb-3">
                                <select wire:model.live="selected_inventory" class="form-control @error('selected_inventory') is-invalid @enderror">
                                    <option value="">Select an item</option>
                                    @foreach($inventory as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach 
                                </select>
                                <input type="number" 
                                       wire:model.live="quantity" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       min="1" 
                                       value="1">
                                <button type="button" 
                                        wire:click="addInventory" 
                                        wire:loading.attr="disabled"
                                        class="btn btn-primary">
                                    <span wire:loading wire:target="addInventory">Adding...</span>
                                    <span wire:loading.remove>Add</span>
                                </button>
                            </div>

                            @error('selected_inventory') <span class="text-danger">{{ $message }}</span> @enderror
                            @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror

                            @if(session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session('message') }}
                                </div>
                            @endif

                            @if(session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Create Job Card
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>  