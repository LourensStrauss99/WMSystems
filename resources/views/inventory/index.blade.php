@extends('layouts.app')
@extends('layouts.nav')
@section('content')

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Inventory List</h1>
    <form method="GET" action="/inventory" class="mb-4">
        <input type="text" name="search" placeholder="Search by name..." value="{{ request('search') }}">
        <button type="submit">Search</button>
    </form>
    <table class="w-full border" border="1">
        <tr>
            <th>Name</th>
            <th>Short Description</th>
            <th>Buying Price</th>
            <th>Selling Price</th>
            <th>Supplier</th>
            <th>Goods Received Voucher</th>
            <th>Stock Level</th>
            <th>Min Level</th>
        </tr>
        @foreach($items as $item)
        <tr onclick="highlightRow(this)">
          
            <td>{{ $item->name }}</td>
            <td>{{ $item->short_description }}</td>
            <td>{{ $item->buying_price }}</td>
            <td>{{ $item->selling_price }}</td>
            <td>{{ $item->supplier }}</td>
            <td>{{ $item->goods_received_voucher }}</td>
            <td>{{ $item->stock_level }}</td>
            <td>{{ $item->min_level }}</td>
        </tr>
        @endforeach
    </table>
</div>



<script>
function highlightRow(row) {
    // Remove highlight from all rows
    document.querySelectorAll('tr.tr-highlight').forEach(function(tr) {
        tr.classList.remove('tr-highlight');
    });
    // Add highlight to the clicked row
    row.classList.add('tr-highlight');
}
</script>
@endsection