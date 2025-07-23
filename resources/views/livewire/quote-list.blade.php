<div>
    <h3 class="mb-3">Quotes List</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Quote #</th>
                <th>Client</th>
                <th>Address</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotes as $quote)
                <tr wire:click="selectQuote({{ $quote->id }})" style="cursor:pointer; @if($selectedQuoteId == $quote->id) background:#e3f2fd; @endif">
                    <td>{{ $quote->quote_number }}</td>
                    <td>{{ $quote->client_name }}</td>
                    <td>{{ $quote->client_address }}</td>
                    <td>{{ $quote->quote_date ? $quote->quote_date->format('Y-m-d') : '' }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ ucfirst($quote->status ?? 'pending') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-info" wire:click="viewQuote" @if(!$selectedQuoteId) disabled @endif>View</button>
        <button class="btn btn-primary" wire:click="sendQuote" @if(!$selectedQuoteId) disabled @endif>Send</button>
        <button class="btn btn-success" wire:click="acceptQuote" @if(!$selectedQuoteId) disabled @endif>Accept</button>
        <button class="btn btn-danger" wire:click="rejectQuote" @if(!$selectedQuoteId) disabled @endif>Reject</button>
        <button class="btn btn-warning" wire:click="amendQuote" @if(!$selectedQuoteId) disabled @endif>Amend</button>
    </div>
</div>
