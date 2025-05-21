<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Inventory Item</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <h1>Add Inventory Item</h1>
    <form method="POST" action="/admin/inventory">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" required><br>
        <label>Short Description:</label>
        <input type="text" name="short_description"><br>
        <label>Buying Price:</label>
        <input type="number" step="0.01" name="buying_price" required><br>
        <label>Selling Price:</label>
        <input type="number" step="0.01" name="selling_price" required><br>
        <label>Supplier:</label>
        <input type="text" name="supplier"><br>
        <label>Goods Received Voucher:</label>
        <input type="text" name="goods_received_voucher" required><br>
        <label>Stock Level:</label>
        <input type="number" name="stock_level" required><br>
        <label>Min Level:</label>
        <input type="number" name="min_level" required><br>
        <button type="submit">Add Inventory</button>
    </form>
</body>
</html>