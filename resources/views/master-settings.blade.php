<!DOCTYPE html>
<html lang="en">
  <!-- In login.html -->
<head>
    <meta charset="UTF-8">
    <title>Admin-Panel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
  <body class="bg-gray-100 font-sans">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin Panel</h1>
        <a href="/client" class="text-white hover:underline">Home</a>
      </div>
    </nav>
    @if ($errors->any())
    <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

    <!-- Main Content -->
    <div class="container mx-auto mt-8 p-4">
      <h2 class="text-2xl font-semibold mb-4 mt-8">Add Inventory Item</h2>
      <form method="POST" action="/admin/inventory" class="bg-white p-6 rounded-lg shadow-md mb-8">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Name</label>
            <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Short Description</label>
            <input type="text" name="short_description" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Buying Price</label>
            <input type="number" step="0.01" name="buying_price" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Selling Price</label>
            <input type="number" step="0.01" name="selling_price" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Supplier</label>
            <input type="text" name="supplier" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Goods Received Voucher</label>
            <input type="text" name="goods_received_voucher" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Stock Level</label>
            <input type="number" name="stock_level" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Min Level</label>
            <input type="number" name="min_level" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">Add Inventory</button>
    </form>

      <!-- Success/Error Message -->
      <div id="responseMessage" class="mt-4 text-center text-lg font-semibold"></div>

      <h2 class="text-2xl font-semibold mb-4 mt-8">Add Employee</h2>
        <form method="POST" action="{{ route('admin.employees.store') }}" class="bg-white p-6 rounded-lg shadow-md mb-8">
            @csrf
            
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Name</label>
        <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Surname</label>
        <input type="text" name="surname" class="w-full px-4 py-2 border rounded-lg" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Telephone</label>
        <input type="text" name="telephone" class="w-full px-4 py-2 border rounded-lg" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Email</label>
        <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Password</label>
        <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Role</label>
        <select name="role" class="w-full px-4 py-2 border rounded-lg" required>
            <option value="admin">Admin</option>
            <option value="artisan">Artisan</option>
            <option value="staff">Staff</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Admin Level</label>
        <select name="admin_level" class="w-full px-4 py-2 border rounded-lg">
            <option value="0">None</option>
            <option value="1">Level 1</option>
            <option value="2">Level 2</option>
            <option value="3">Level 3</option>
        </select>
    </div>
    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700">Add Employee</button>
</form>

    <!-- JavaScript -->
    <!--<script src="../src/js/admin-panel.js"></script> -->
  </body>
</html>