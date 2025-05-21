
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