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
@if(session('success'))
    <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
        {{ session('success') }}
    </div>
@endif

    <!-- Main Content -->
    <div class="container mx-auto mt-8 p-4">
      <!-- Enhanced Add Inventory form in master-settings.blade.php -->

<h2 class="text-2xl font-semibold mb-4 mt-8">📦 Stock Replenishment</h2>
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Select Existing Item to Replenish</label>
        <select id="existing_item_select" class="w-full px-4 py-2 border rounded-lg">
            <option value="">-- Select an existing item --</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}" 
                        data-name="{{ $item->name }}"

                        data-short-code="{{ $item->short_code }}"

                        data-vendor="{{ $item->vendor }}"

                        data-supplier="{{ $item->supplier }}"

                        data-buying-price="{{ $item->buying_price }}"

                        data-selling-price="{{ $item->selling_price }}"

                        data-current-stock="{{ $item->stock_level }}"

                        data-min-level="{{ $item->min_level }}">
                    [{{ $item->short_code }}] {{ $item->name }} (Current Stock: {{ $item->stock_level }})
                </option>
            @endforeach
        </select>
    </div>
    
    <div id="current_stock_info" class="alert alert-info" style="display: none;">
        <strong>📊 Current Stock Information:</strong>
        <div id="stock_details"></div>
    </div>
</div>

<!-- Enhanced Add Inventory form with auto-populate capability -->
<h2 class="text-2xl font-semibold mb-4 mt-8">
    <span id="form_title">Add New Inventory Item</span>
    <button type="button" id="clear_form" class="ml-4 bg-gray-500 text-white px-4 py-2 rounded text-sm" style="display: none;" onclick="clearForm()">
        🔄 Clear Form (Add New Item)
    </button>
</h2>
<form method="POST" action="/admin/inventory" class="bg-white p-6 rounded-lg shadow-md mb-8" id="inventory_form">
    @csrf
    
    <!-- Hidden field to track if this is a replenishment -->
    <input type="hidden" id="is_replenishment" name="is_replenishment" value="0">
    <input type="hidden" id="original_item_id" name="original_item_id" value="">
    
    <!-- Basic Item Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Name *</label>
            <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Short Code *</label>
            <input type="text" name="short_code" id="short_code" class="w-full px-4 py-2 border rounded-lg" placeholder="e.g., CAB-0016" required>
            <small class="text-blue-600" id="code_note" style="display: none;">💡 Code will be auto-generated for replenishment</small>
        </div>
    </div>
    
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Description *</label>
        <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg" rows="3" placeholder="Full description of the item..." required></textarea>
    </div>
    
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Short Description</label>
        <input type="text" name="short_description" id="short_description" class="w-full px-4 py-2 border rounded-lg" placeholder="Brief description or code">
    </div>
    
    <!-- Supplier Information -->
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">🏢 Supplier Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Vendor</label>
            <input type="text" name="vendor" id="vendor" class="w-full px-4 py-2 border rounded-lg" placeholder="Vendor name">
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Supplier</label>
            <input type="text" name="supplier" id="supplier" class="w-full px-4 py-2 border rounded-lg" placeholder="Supplier name">
        </div>
    </div>
    
    <!-- Purchase Documentation -->
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">📋 Purchase Documentation</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Invoice Number</label>
            <input type="text" name="invoice_number" id="invoice_number" class="w-full px-4 py-2 border rounded-lg" placeholder="Invoice/Bill number">
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Receipt Number</label>
            <input type="text" name="receipt_number" id="receipt_number" class="w-full px-4 py-2 border rounded-lg" placeholder="Receipt number">
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Purchase Date</label>
            <input type="date" name="purchase_date" id="purchase_date" class="w-full px-4 py-2 border rounded-lg" value="{{ date('Y-m-d') }}">
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Purchase Order Number</label>
            <input type="text" name="purchase_order_number" id="purchase_order_number" class="w-full px-4 py-2 border rounded-lg" placeholder="PO number">
        </div>
    </div>
    
    <div class="mb-6">
        <label class="block text-gray-700 font-bold mb-2">Goods Received Voucher</label>
        <input type="text" name="goods_received_voucher" id="goods_received_voucher" class="w-full px-4 py-2 border rounded-lg" placeholder="GRV number or reference">
    </div>
    
    <div class="mb-6">
        <label class="block text-gray-700 font-bold mb-2">Purchase Notes</label>
        <textarea name="purchase_notes" id="purchase_notes" class="w-full px-4 py-2 border rounded-lg" rows="2" placeholder="Additional notes about this purchase..."></textarea>
    </div>
    
    <!-- Pricing Information -->
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">💰 Pricing Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Buying Price (R) *</label>
            <input type="number" step="0.01" name="buying_price" id="buying_price" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Selling Price (R) *</label>
            <input type="number" step="0.01" name="selling_price" id="selling_price" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
    </div>
    
    <!-- Stock Information -->
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">📦 Stock Information</h3>
    
    <!-- Show current stock info when replenishing -->
    <div id="replenishment_info" class="bg-blue-50 p-4 rounded-lg mb-4" style="display: none;">
        <h4 class="font-semibold text-blue-800 mb-2">📊 Current Stock Status</h4>
        <div id="current_stock_display"></div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">
                <span id="stock_label">Stock Level *</span>
            </label>
            <input type="number" name="stock_level" id="stock_level" class="w-full px-4 py-2 border rounded-lg" required min="0">
            <small class="text-gray-600" id="stock_help">Enter the quantity you're adding to inventory</small>
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Minimum Level *</label>
            <input type="number" name="min_level" id="min_level" class="w-full px-4 py-2 border rounded-lg" required min="0">
        </div>
    </div>
    
    <div class="mb-6">
        <label class="block text-gray-700 font-bold mb-2">Stock Update Reason</label>
        <input type="text" name="stock_update_reason" id="stock_update_reason" class="w-full px-4 py-2 border rounded-lg" value="Initial stock entry" placeholder="Reason for this stock level">
    </div>
    
    <!-- Hidden fields -->
    <input type="hidden" name="nett_price" id="nett_price">
    <input type="hidden" name="sell_price" id="sell_price">
    <input type="hidden" name="quantity" id="quantity">
    <input type="hidden" name="min_quantity" id="min_quantity">
    <input type="hidden" name="stock_added" id="stock_added">
    <input type="hidden" name="last_stock_update" id="last_stock_update">
    
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg shadow-md hover:bg-blue-700 font-semibold" id="submit_btn">
            📦 Add Inventory Item
        </button>
    </div>
</form>

<script>
// Populate form when existing item is selected
document.getElementById('existing_item_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const itemId = this.value;
    
    if (itemId) {
        // Show that this is a replenishment
        document.getElementById('form_title').textContent = 'Replenish Stock: ' + selectedOption.dataset.name;
        document.getElementById('clear_form').style.display = 'inline-block';
        document.getElementById('is_replenishment').value = '1';
        document.getElementById('original_item_id').value = itemId;
        
        // Populate form fields
        document.getElementById('name').value = selectedOption.dataset.name;
        document.getElementById('description').value = selectedOption.dataset.description;
        document.getElementById('short_description').value = selectedOption.dataset.shortDescription;
        document.getElementById('vendor').value = selectedOption.dataset.vendor;
        document.getElementById('supplier').value = selectedOption.dataset.supplier;
        document.getElementById('buying_price').value = selectedOption.dataset.buyingPrice;
        document.getElementById('selling_price').value = selectedOption.dataset.sellingPrice;
        document.getElementById('min_level').value = selectedOption.dataset.minLevel;
        
        // Generate new short code for replenishment (add suffix)
        const originalCode = selectedOption.dataset.shortCode;
        const timestamp = new Date().toISOString().slice(5, 10).replace('-', ''); // MMDD format
        document.getElementById('short_code').value = originalCode + '-R' + timestamp;
        document.getElementById('code_note').style.display = 'block';
        
        // Update labels and help text
        document.getElementById('stock_label').innerHTML = 'New Stock Quantity * <span class="text-blue-600">(Adding to existing)</span>';
        document.getElementById('stock_help').textContent = 'Enter the quantity you\'re adding (not total stock)';
        document.getElementById('stock_update_reason').value = 'Stock replenishment - ' + new Date().toLocaleDateString();
        document.getElementById('submit_btn').innerHTML = '📦 Add Replenishment Stock';
        
        // Show current stock info
        const currentStock = selectedOption.dataset.currentStock;
        const minLevel = selectedOption.dataset.minLevel;
        document.getElementById('current_stock_display').innerHTML = `
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><strong>Current Stock:</strong> ${currentStock}</div>
                <div><strong>Minimum Level:</strong> ${minLevel}</div>
                <div><strong>Status:</strong> ${currentStock <= minLevel ? '<span class="text-red-600">⚠️ Below Minimum</span>' : '<span class="text-green-600">✅ Above Minimum</span>'}</div>
                <div><strong>New Code:</strong> ${originalCode}-R${timestamp}</div>
            </div>
        `;
        document.getElementById('replenishment_info').style.display = 'block';
        
        // Show current stock details
        document.getElementById('stock_details').innerHTML = `
            <div class="mt-2">
                <strong>Item:</strong> [${originalCode}] ${selectedOption.dataset.name}<br>
                <strong>Current Stock:</strong> ${currentStock} units<br>
                <strong>Minimum Level:</strong> ${minLevel} units<br>
                <strong>Status:</strong> ${currentStock <= minLevel ? '<span class="text-red-600">⚠️ Needs Replenishment</span>' : '<span class="text-green-600">✅ Stock Level OK</span>'}
            </div>
        `;
        document.getElementById('current_stock_info').style.display = 'block';
        
        // Clear purchase documentation fields for new entry
        document.getElementById('invoice_number').value = '';
        document.getElementById('receipt_number').value = '';
        document.getElementById('purchase_order_number').value = '';
        document.getElementById('goods_received_voucher').value = '';
        document.getElementById('purchase_notes').value = '';
        document.getElementById('purchase_date').value = new Date().toISOString().split('T')[0];
        
    } else {
        clearForm();
    }
});

function clearForm() {
    // Reset form title and button
    document.getElementById('form_title').textContent = 'Add New Inventory Item';
    document.getElementById('clear_form').style.display = 'none';
    document.getElementById('submit_btn').innerHTML = '📦 Add Inventory Item';
    
    // Reset hidden tracking fields
    document.getElementById('is_replenishment').value = '0';
    document.getElementById('original_item_id').value = '';
    
    // Reset labels
    document.getElementById('stock_label').textContent = 'Stock Level *';
    document.getElementById('stock_help').textContent = 'Enter the quantity you\'re adding to inventory';
    document.getElementById('code_note').style.display = 'none';
    
    // Hide info panels
    document.getElementById('current_stock_info').style.display = 'none';
    document.getElementById('replenishment_info').style.display = 'none';
    
    // Clear form
    document.getElementById('inventory_form').reset();
    document.getElementById('existing_item_select').value = '';
    document.getElementById('purchase_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('stock_update_reason').value = 'Initial stock entry';
}

// Auto-fill hidden fields when form is submitted
document.getElementById('inventory_form').addEventListener('submit', function(e) {
    // Set derived prices
    document.getElementById('nett_price').value = document.getElementById('buying_price').value;
    document.getElementById('sell_price').value = document.getElementById('selling_price').value;
    
    // Set stock quantities
    const stockLevel = document.getElementById('stock_level').value;
    document.getElementById('quantity').value = stockLevel;
    document.getElementById('min_quantity').value = document.getElementById('min_level').value;
    document.getElementById('stock_added').value = stockLevel;
    document.getElementById('last_stock_update').value = new Date().toISOString().split('T')[0];
});
</script>

      <!-- Success/Error Message -->
      <div id="responseMessage" class="mt-4 text-center text-lg font-semibold"></div>

      <!-- Enhanced Employee Management Section -->
<h2 class="text-2xl font-semibold mb-4 mt-8">👥 Add Employee</h2>
<form method="POST" action="{{ route('admin.employees.store') }}" class="bg-white p-6 rounded-lg shadow-md mb-8">
    @csrf
    
    <!-- Personal Information -->
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">👤 Personal Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Name *</label>
            <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Surname *</label>
            <input type="text" name="surname" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
    </div>
    
    <!-- Contact Information -->
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">📞 Contact Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Telephone *</label>
            <input type="text" id="telephone" name="telephone" 
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   required pattern="^\+?[0-9]{7,20}$" placeholder="+27721234567">
            <span id="telephone-error" class="text-red-500 text-xs mt-1 block"></span>
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Email *</label>
            <input type="email" id="email" name="email" 
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   required placeholder="employee@company.com">
            <span id="email-error" class="text-red-500 text-xs mt-1 block"></span>
        </div>
    </div>
    
    <!-- Security & Access -->
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">🔐 Security & Access</h3>
    <div class="mb-6">
        <label class="block text-gray-700 font-bold mb-2">Password *</label>
        <input type="password" name="password" 
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
               required placeholder="Enter secure password" minlength="6">
        <small class="text-gray-600 text-xs">Password must be at least 6 characters long</small>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2">Role *</label>
            <select name="role" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">-- Select Role --</option>
                <option value="admin">👑 Admin</option>
                <option value="artisan">🔧 Artisan</option>
                <option value="staff">👤 Staff</option>
            </select>
        </div>
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Admin Level</label>
            <select name="admin_level" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="0">None</option>
                <option value="1">Level 1 - Basic Admin</option>
                <option value="2">Level 2 - Advanced Admin</option>
                <option value="3">Level 3 - Super Admin</option>
            </select>
            <small class="text-gray-600 text-xs">Admin level determines system permissions</small>
        </div>
    </div>
    
    <!-- Role-based permissions info -->
    <div class="bg-blue-50 p-4 rounded-lg mb-6">
        <h4 class="font-semibold text-blue-800 mb-2">📋 Role Permissions</h4>
        <div class="text-sm text-blue-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <strong>👑 Admin:</strong><br>
                    • Full system access<br>
                    • User management<br>
                    • System settings
                </div>
                <div>
                    <strong>🔧 Artisan:</strong><br>
                    • Job management<br>
                    • Inventory access<br>
                    • Time tracking
                </div>
                <div>
                    <strong>👤 Staff:</strong><br>
                    • Basic job access<br>
                    • Limited inventory<br>
                    • Own profile only
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg shadow-md hover:bg-green-700 font-semibold transition-colors duration-200">
            👥 Add Employee
        </button>
    </div>
</form>

<!-- Enhanced Company Details Button -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h3 class="text-lg font-semibold mb-3 text-gray-800 border-b pb-2">🏢 Company Management</h3>
    <p class="text-gray-600 mb-4">Update your company information, contact details, and business settings.</p>
    <a href="{{ route('company.details') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg shadow-md hover:bg-blue-700 font-semibold transition-colors duration-200 inline-block">
        🏢 Edit Company Details
    </a>
</div>

    <!-- JavaScript -->
    <!--<script src="../src/js/admin-panel.js"></script> -->
    <script>
// Enhanced telephone validation
document.getElementById('telephone').addEventListener('input', function() {
    const value = this.value;
    const regex = /^\+?[0-9]{7,20}$/;
    const errorSpan = document.getElementById('telephone-error');
    const inputField = this;
    
    if (value && !regex.test(value)) {
        errorSpan.textContent = '⚠️ Enter a valid telephone number (e.g. +27721234567)';
        inputField.classList.add('border-red-500');
        inputField.classList.remove('border-green-500');
    } else if (value) {
        errorSpan.textContent = '✅ Valid telephone number';
        errorSpan.classList.remove('text-red-500');
        errorSpan.classList.add('text-green-500');
        inputField.classList.add('border-green-500');
        inputField.classList.remove('border-red-500');
    } else {
        errorSpan.textContent = '';
        inputField.classList.remove('border-red-500', 'border-green-500');
    }
});

// Enhanced email validation
document.getElementById('email').addEventListener('input', function() {
    const value = this.value;
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const errorSpan = document.getElementById('email-error');
    const inputField = this;
    
    if (value && !regex.test(value)) {
        errorSpan.textContent = '⚠️ Enter a valid email address';
        inputField.classList.add('border-red-500');
        inputField.classList.remove('border-green-500');
    } else if (value) {
        errorSpan.textContent = '✅ Valid email address';
        errorSpan.classList.remove('text-red-500');
        errorSpan.classList.add('text-green-500');
        inputField.classList.add('border-green-500');
        inputField.classList.remove('border-red-500');
    } else {
        errorSpan.textContent = '';
        inputField.classList.remove('border-red-500', 'border-green-500');
    }
});

// Password strength indicator
document.querySelector('input[name="password"]').addEventListener('input', function() {
    const password = this.value;
    const strength = checkPasswordStrength(password);
    
    // You can add a password strength indicator here if needed
    if (password.length < 6) {
        this.classList.add('border-red-500');
        this.classList.remove('border-green-500');
    } else {
        this.classList.add('border-green-500');
        this.classList.remove('border-red-500');
    }
});

function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    return strength;
}

// Role selection helper
document.querySelector('select[name="role"]').addEventListener('change', function() {
    const adminLevelSelect = document.querySelector('select[name="admin_level"]');
    
    if (this.value === 'admin') {
        adminLevelSelect.disabled = false;
        adminLevelSelect.classList.remove('bg-gray-100');
    } else {
        adminLevelSelect.value = '0';
        adminLevelSelect.disabled = true;
        adminLevelSelect.classList.add('bg-gray-100');
    }
});

// Form submission enhancement
document.querySelector('form[action="{{ route('admin.employees.store') }}"]').addEventListener('submit', function(e) {
    // Add loading state to button
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '⏳ Adding Employee...';
    submitBtn.disabled = true;
    
    // Re-enable button after 3 seconds in case of error
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Auto-fill hidden fields for inventory form (keep existing functionality)
document.querySelector('form[action="/admin/inventory"]').addEventListener('submit', function(e) {
    // Set nett_price and sell_price to match buying_price and selling_price
    document.getElementById('nett_price').value = document.querySelector('input[name="buying_price"]').value;
    document.getElementById('sell_price').value = document.querySelector('input[name="selling_price"]').value;
    
    // Set quantity and min_quantity to match stock_level and min_level
    document.getElementById('quantity').value = document.querySelector('input[name="stock_level"]').value;
    document.getElementById('min_quantity').value = document.querySelector('input[name="min_level"]').value;
});
</script>
  </body>
</html>