@extends('layouts.app')

@section('content')
<div class="container bg-white p-4 rounded shadow" style="max-width: 700px; margin: auto; font-family: 'Arial', sans-serif;">
    <h2 class="mb-3">Edit Quote</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('quotes.update', $quote->id) }}" id="quote-edit-form">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Quote Number</label>
            <input type="text" class="form-control" value="{{ $quote->quote_number }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Quote Date</label>
            <input type="date" class="form-control" name="quote_date" value="{{ old('quote_date', $quote->quote_date ? $quote->quote_date->format('Y-m-d') : '') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Client Name</label>
            <input type="text" class="form-control" name="client_name" value="{{ $quote->client_name }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Client Address</label>
            <input type="text" class="form-control" name="client_address" value="{{ $quote->client_address }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Client Email</label>
            <input type="email" class="form-control" name="client_email" value="{{ $quote->client_email }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Client Telephone</label>
            <input type="text" class="form-control" name="client_telephone" value="{{ $quote->client_telephone }}">
        </div>
        <h5 class="mt-4">Quote Items</h5>
        <table class="table table-bordered" id="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $i => $item)
                    <tr>
                        <td><input type="text" name="items[{{ $i }}][description]" class="form-control" value="{{ $item['description'] ?? '' }}" placeholder="Item description"></td>
                        <td><input type="number" name="items[{{ $i }}][qty]" class="form-control" value="{{ $item['qty'] ?? $item['quantity'] ?? '' }}"></td>
                        <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control" value="{{ $item['unit_price'] ?? '' }}"></td>
                        <td><input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control" value="{{ $item['total'] ?? '' }}"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-success btn-sm" id="add-item">Add Item</button>
        <div class="mb-3 mt-4">
            <label class="form-label">Notes / Terms</label>
            <textarea class="form-control" name="notes">{{ $quote->notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
    document.getElementById('add-item').addEventListener('click', function() {
        const rowCount = table.rows.length;
        const row = table.insertRow();
        row.innerHTML = `
            <td><input type="text" name="items[ ${rowCount} ][description]" class="form-control" placeholder="Item description"></td>
            <td><input type="number" name="items[ ${rowCount} ][qty]" class="form-control"></td>
            <td><input type="number" step="0.01" name="items[ ${rowCount} ][unit_price]" class="form-control"></td>
            <td><input type="number" step="0.01" name="items[ ${rowCount} ][total]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
        `.replace(/\u0000/g, rowCount);
    });
    table.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection 