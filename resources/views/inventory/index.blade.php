<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <link rel="stylesheet" href="/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="logo">Client</div>
        <nav class="tabs">
            <a href="/client" class="tab-button">1 - Client</a>
            <a href="/jobcard" class="tab-button">2 - Jobcard</a>
            <a href="/progress" class="tab-button">3 - Progress</a>
            <a href="/invoice" class="tab-button">4 - Invoices</a>
            <a href="/artisanprogress" class="tab-button">5 - Artisan progress</a>
            <a href="/inventory" class="tab-button active">6 - Inventory</a>
            <a href="/reports" class="tab-button">7 - Reports</a>
            <a href="/quotes" class="tab-button">8 - Quotes</a>
            <a href="/settings" class="tab-button">9 - Settings</a>
        </nav>
    </header>

    <h1>Inventory List</h1>
    <form method="GET" action="/inventory">
        <input type="text" name="search" placeholder="Search by name..." value="{{ request('search') }}">
        <button type="submit">Search</button>
    </form>
    <table border="1">
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
        <tr>
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
</body>
</html>