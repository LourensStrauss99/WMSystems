<div>
    {{-- Success Message --}}
    @if ($successMessage)
        <div class="alert alert-success">
            {{ $successMessage }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-1/2">
                {{-- Existing fields --}}
                <div class="form-group">
                    <label for="jobcard_number">Jobcard Number</label>
                    <input type="text" class="form-control" id="jobcard_number" wire:model="jobcard_number" readonly>
                </div>
                <div class="form-group">
                    <label for="job_date">Job Date</label>
                    <input type="date" class="form-control" id="job_date" wire:model="job_date">
                </div>
                <div class="form-group">
                    <label for="clientId">Select Existing Client (optional)</label>
                    <select class="form-control" id="clientId" wire:model.live="clientId">
                        <option value="">-- None --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} {{ $client->surname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" wire:model="clientFields.name">
                </div>
                <div class="form-group">
                    <label for="surname">Surname</label>
                    <input type="text" class="form-control" id="surname" wire:model="clientFields.surname">
                </div>
                <div class="form-group">
                    <label for="telephone">Telephone</label>
                    <input type="text" class="form-control" id="telephone" wire:model="clientFields.telephone">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" wire:model="clientFields.address">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" wire:model="clientFields.email">
                </div>
                {{-- Show Save Client button if no client is selected and all fields are filled 
                @if(!$clientId && $clientFields['name'] && $clientFields['surname'] && $clientFields['telephone'] && $clientFields['address'] && $clientFields['email'])
                    <button type="button" class="btn btn-primary" wire:click="saveNewClient">Save Client</button>
                @endif--}}
            </div>
            <div class="w-full md:w-1/2 flex flex-col gap-4">
                <div>
                    <label for="work_request" class="block font-medium">Work Request</label>
                    <textarea class="form-control w-full" id="work_request" wire:model="work_request" rows="6"></textarea>
                </div>
                <div>
                    <label for="special_request" class="block font-medium">Special Instructions</label>
                    <textarea class="form-control w-full" id="special_request" wire:model="special_request" rows="6"></textarea>
                </div>
            </div>
        </div>
        <button type="submit" class="btn px-4 py-2 mt-3" style="background-color: #bfdbfe; color: #222; border: 1px solid #60a5fa;">
            Submit
        </button>
    </form>
</div>