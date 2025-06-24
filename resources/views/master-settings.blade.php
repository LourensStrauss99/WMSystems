<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .card-hover:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
        }
        
        .procurement-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stock-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .employee-section {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .company-section {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">
                <i class="fas fa-cogs me-2"></i>Admin Panel
            </h1>
            <div class="flex items-center space-x-4">
                <a href="/inventory" class="text-white hover:text-blue-200 transition-colors">
                    <i class="fas fa-boxes me-1"></i>Inventory
                </a>
                <a href="/client" class="text-white hover:text-blue-200 transition-colors">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </div>
        </div>
    </nav>

    <!-- Error/Success Messages -->
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-4 mt-4 rounded shadow">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Please fix the following errors:</strong>
            </div>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif 

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-4 mt-4 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="container mx-auto mt-8 p-4">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-tools text-blue-600 mr-3"></i>
                Master Settings & Management
            </h2>
            <p class="text-gray-600">Comprehensive system management and inventory control</p>
        </div>

        <!-- Quick Action Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Purchase Orders Card -->
            <div class="bg-white rounded-lg shadow-lg card-hover overflow-hidden">
                <div class="procurement-section text-white p-4 text-center">
                    <i class="fas fa-file-invoice fa-3x mb-3"></i>
                    <h3 class="text-lg font-bold">Purchase Orders</h3>
                    <p class="text-sm opacity-90">Create & manage purchase orders</p>
                </div>
                <div class="p-4 text-center">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-primary btn-sm mb-2 w-full">
                        <i class="fas fa-list mr-1"></i>View All POs
                    </a>
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-outline-primary btn-sm w-full">
                        <i class="fas fa-plus mr-1"></i>Create New PO
                    </a>
                </div>
            </div>

            <!-- Suppliers Card -->
            <div class="bg-white rounded-lg shadow-lg card-hover overflow-hidden">
                <div class="bg-green-500 text-white p-4 text-center">
                    <i class="fas fa-building fa-3x mb-3"></i>
                    <h3 class="text-lg font-bold">Suppliers</h3>
                    <p class="text-sm opacity-90">Manage supplier information</p>
                </div>
                <div class="p-4 text-center">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-success btn-sm mb-2 w-full">
                        <i class="fas fa-users mr-1"></i>View Suppliers
                    </a>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-outline-success btn-sm w-full">
                        <i class="fas fa-user-plus mr-1"></i>Add Supplier
                    </a>
                </div>
            </div>

            <!-- GRV System Card -->
            <div class="bg-white rounded-lg shadow-lg card-hover overflow-hidden">
                <div class="bg-warning text-dark p-4 text-center">
                    <i class="fas fa-truck-loading fa-3x mb-3"></i>
                    <h3 class="text-lg font-bold">GRV System</h3>
                    <p class="text-sm opacity-75">Goods received vouchers</p>
                </div>
                <div class="p-4 text-center">
                    <a href="{{ route('grv.index') }}" class="btn btn-warning btn-sm mb-2 w-full">
                        <i class="fas fa-clipboard-check mr-1"></i>View GRVs
                    </a>
                    <button class="btn btn-outline-warning btn-sm w-full" onclick="showLowStockItems()">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Check Stock
                    </button>
                </div>
            </div>

            <!-- Inventory Card -->
            <div class="bg-white rounded-lg shadow-lg card-hover overflow-hidden">
                <div class="bg-info text-white p-4 text-center">
                    <i class="fas fa-boxes fa-3x mb-3"></i>
                    <h3 class="text-lg font-bold">Inventory</h3>
                    <p class="text-sm opacity-90">View and manage stock</p>
                </div>
                <div class="p-4 text-center">
                    <a href="{{ route('inventory.index') }}" class="btn btn-info btn-sm mb-2 w-full">
                        <i class="fas fa-warehouse mr-1"></i>View Inventory
                    </a>
                    <button class="btn btn-outline-info btn-sm w-full" onclick="scrollToInventoryForm()">
                        <i class="fas fa-plus mr-1"></i>Add Items
                    </button>
                </div>
            </div>
        </div>

        <!-- Procurement Quick Actions -->
        <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
            <div class="procurement-section text-white p-4">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    Procurement Quick Actions
                </h3>
                <p class="opacity-90">Fast access to procurement management tools</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="createPOFromStock()" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition-colors">
                        <i class="fas fa-magic fa-2x mb-2"></i>
                        <div class="font-bold">Quick PO from Stock</div>
                        <div class="text-sm opacity-90">Create PO for low stock items</div>
                    </button>
                    
                    <button onclick="showLowStockItems()" class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center transition-colors">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <div class="font-bold">Low Stock Alert</div>
                        <div class="text-sm opacity-90">View items needing replenishment</div>
                    </button>
                    
                    <button onclick="createPOForSelected()" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition-colors">
                        <i class="fas fa-file-plus fa-2x mb-2"></i>
                        <div class="font-bold">PO for Selected Item</div>
                        <div class="text-sm opacity-90">Create PO for chosen inventory</div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stock Replenishment Section -->
        <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden" id="stock-section">
            <div class="stock-section text-white p-4">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-box-open mr-3"></i>
                    Stock Replenishment
                </h3>
                <p class="opacity-90">Select existing items to replenish or add new inventory</p>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-search mr-2"></i>Select Existing Item to Replenish
                    </label>
                    <div class="flex gap-4">
                        <select id="existing_item_select" class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
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
                                    [{{ $item->short_code }}] {{ $item->name }} (Stock: {{ $item->stock_level }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors" onclick="createPOForSelected()">
                            <i class="fas fa-file-plus mr-1"></i>Create PO
                        </button>
                    </div>
                </div>
                
                <div id="current_stock_info" class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded" style="display: none;">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <strong class="text-blue-800">Current Stock Information:</strong>
                    </div>
                    <div id="stock_details" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Add/Replenish Inventory Form -->
        <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden" id="inventory-form-section">
            <div class="bg-gray-600 text-white p-4 flex justify-between items-center">
                <h3 class="text-xl font-bold flex items-center" id="form_title">
                    <i class="fas fa-plus mr-3"></i>Add New Inventory Item
                </h3>
                <button type="button" id="clear_form" class="bg-gray-500 hover:bg-gray-400 text-white px-4 py-2 rounded transition-colors" style="display: none;" onclick="clearForm()">
                    <i class="fas fa-redo mr-1"></i>Clear Form (Add New Item)
                </button>
            </div>
            
            <form method="POST" action="/admin/inventory" class="p-6" id="inventory_form">
                @csrf
                
                <!-- Hidden tracking fields -->
                <input type="hidden" id="is_replenishment" name="is_replenishment" value="0">
                <input type="hidden" id="original_item_id" name="original_item_id" value="">
                
                <!-- Basic Item Information -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                        <i class="fas fa-tag mr-2 text-blue-600"></i>Basic Item Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Item Name *</label>
                            <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Short Code *</label>
                            <input type="text" name="short_code" id="short_code" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., CAB-0016" required>
                            <small class="text-blue-600 text-sm" id="code_note" style="display: none;">
                                <i class="fas fa-info-circle mr-1"></i>Code will be auto-generated for replenishment
                            </small>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-3">
                            <label class="block text-gray-700 font-bold mb-2">Description *</label>
                            <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Full description of the item..." required></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Short Description</label>
                            <input type="text" name="short_description" id="short_description" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Brief description">
                        </div>
                    </div>
                </div>
                
                <!-- Supplier Information -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                        <i class="fas fa-building mr-2 text-green-600"></i>Supplier Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Vendor</label>
                            <input type="text" name="vendor" id="vendor" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Vendor name">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Supplier</label>
                            <input type="text" name="supplier" id="supplier" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Supplier name">
                        </div>
                    </div>
                </div>
                
                <!-- Purchase Documentation -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                        <i class="fas fa-file-alt mr-2 text-purple-600"></i>Purchase Documentation
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Invoice Number</label>
                            <input type="text" name="invoice_number" id="invoice_number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Invoice/Bill number">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Receipt Number</label>
                            <input type="text" name="receipt_number" id="receipt_number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Receipt number">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Purchase Date</label>
                            <input type="date" name="purchase_date" id="purchase_date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Purchase Order Number</label>
                            <input type="text" name="purchase_order_number" id="purchase_order_number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="PO number">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Goods Received Voucher</label>
                            <input type="text" name="goods_received_voucher" id="goods_received_voucher" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="GRV number">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Purchase Notes</label>
                        <textarea name="purchase_notes" id="purchase_notes" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Additional notes about this purchase..."></textarea>
                    </div>
                </div>
                
                <!-- Pricing Information -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                        <i class="fas fa-money-bill-wave mr-2 text-yellow-600"></i>Pricing Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Buying Price (R) *</label>
                            <div class="flex">
                                <span class="bg-gray-200 px-3 py-2 border border-r-0 rounded-l-lg">R</span>
                                <input type="number" step="0.01" name="buying_price" id="buying_price" class="flex-1 px-4 py-2 border rounded-r-lg focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Selling Price (R) *</label>
                            <div class="flex">
                                <span class="bg-gray-200 px-3 py-2 border border-r-0 rounded-l-lg">R</span>
                                <input type="number" step="0.01" name="selling_price" id="selling_price" class="flex-1 px-4 py-2 border rounded-r-lg focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Information -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                        <i class="fas fa-boxes mr-2 text-red-600"></i>Stock Information
                    </h4>
                    
                    <!-- Replenishment Info -->
                    <div id="replenishment_info" class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-4" style="display: none;">
                        <h5 class="font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i>Current Stock Status
                        </h5>
                        <div id="current_stock_display"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2" id="stock_label">Stock Level *</label>
                            <input type="number" name="stock_level" id="stock_level" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required min="0">
                            <small class="text-gray-600" id="stock_help">Enter the quantity you're adding to inventory</small>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Minimum Level *</label>
                            <input type="number" name="min_level" id="min_level" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required min="0">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-gray-700 font-bold mb-2">Stock Update Reason</label>
                        <input type="text" name="stock_update_reason" id="stock_update_reason" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="Initial stock entry" placeholder="Reason for this stock level">
                    </div>
                </div>
                
                <!-- Hidden fields for compatibility -->
                <input type="hidden" name="nett_price" id="nett_price">
                <input type="hidden" name="sell_price" id="sell_price">
                <input type="hidden" name="quantity" id="quantity">
                <input type="hidden" name="min_quantity" id="min_quantity">
                <input type="hidden" name="stock_added" id="stock_added">
                <input type="hidden" name="last_stock_update" id="last_stock_update">
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-md font-semibold transition-colors" id="submit_btn">
                        <i class="fas fa-plus mr-2"></i>Add Inventory Item
                    </button>
                </div>
            </form>
        </div>

        <!-- Employee Management Section -->
        <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
            <div class="employee-section text-white p-4">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-users mr-3"></i>Employee Management
                </h3>
                <p class="opacity-90">Add new employees and manage user accounts</p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.employees.store') }}">
                    @csrf
                    
                    <!-- Personal Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                            <i class="fas fa-user mr-2 text-blue-600"></i>Personal Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Name *</label>
                                <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Surname *</label>
                                <input type="text" name="surname" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                            <i class="fas fa-phone mr-2 text-green-600"></i>Contact Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Telephone *</label>
                                <input type="text" id="telephone" name="telephone" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required pattern="^\+?[0-9]{7,20}$" placeholder="+27721234567">
                                <div id="telephone-error" class="text-red-500 text-sm mt-1"></div>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Email *</label>
                                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required placeholder="employee@company.com">
                                <div id="email-error" class="text-red-500 text-sm mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Security & Access -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                            <i class="fas fa-lock mr-2 text-yellow-600"></i>Security & Access
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Password *</label>
                                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required placeholder="Enter secure password" minlength="6">
                                <small class="text-gray-600">Password must be at least 6 characters long</small>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Role *</label>
                                <select name="role" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="admin">👑 Admin</option>
                                    <option value="artisan">🔧 Artisan</option>
                                    <option value="staff">👤 Staff</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Admin Level</label>
                                <select name="admin_level" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="0">None</option>
                                    <option value="1">Level 1 - Basic Admin</option>
                                    <option value="2">Level 2 - Advanced Admin</option>
                                    <option value="3">Level 3 - Super Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Role Info -->
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6">
                        <h5 class="font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Role Permissions
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <strong class="text-blue-800">👑 Admin:</strong><br>
                                • Full system access<br>
                                • User management<br>
                                • System settings
                            </div>
                            <div>
                                <strong class="text-blue-800">🔧 Artisan:</strong><br>
                                • Job management<br>
                                • Inventory access<br>
                                • Time tracking
                            </div>
                            <div>
                                <strong class="text-blue-800">👤 Staff:</strong><br>
                                • Basic job access<br>
                                • Limited inventory<br>
                                • Own profile only
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg shadow-md font-semibold transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Add Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Company Management Section -->
        <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
            <div class="company-section text-dark p-4">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-building mr-3"></i>Company Management
                </h3>
                <p class="opacity-75">Update your company information, contact details, and business settings</p>
            </div>
            <div class="p-6 text-center">
                <p class="text-gray-600 mb-4">Manage your company profile, contact information, and business preferences.</p>
                <a href="{{ route('company.details') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-8 py-3 rounded-lg shadow-md font-semibold transition-colors inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit Company Details
                </a>
            </div>
        </div>
    </div>

    <!-- Low Stock Modal -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden" id="lowStockModal">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-96 overflow-hidden">
                <div class="bg-orange-500 text-white p-4 flex justify-between items-center">
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Items
                    </h5>
                    <button type="button" class="text-white hover:text-gray-200" onclick="closeModal('lowStockModal')">
                        <i class="fas fa-times fa-lg"></i>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto max-h-80" id="lowStockContent">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2">Loading...</p>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 flex justify-end space-x-2">
                    <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors" onclick="closeModal('lowStockModal')">Close</button>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors" onclick="createPOForLowStock()">
                        <i class="fas fa-file-plus mr-1"></i>Create PO for All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Existing form functionality remains the same
        document.getElementById('existing_item_select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const itemId = this.value;
            
            if (itemId) {
                // Show that this is a replenishment
                document.getElementById('form_title').innerHTML = '<i class="fas fa-plus mr-3"></i>Replenish Stock: ' + selectedOption.dataset.name;
                document.getElementById('clear_form').style.display = 'inline-block';
                document.getElementById('is_replenishment').value = '1';
                document.getElementById('original_item_id').value = itemId;
                
                // Populate form fields
                document.getElementById('name').value = selectedOption.dataset.name;
                document.getElementById('vendor').value = selectedOption.dataset.vendor;
                document.getElementById('supplier').value = selectedOption.dataset.supplier;
                document.getElementById('buying_price').value = selectedOption.dataset.buyingPrice;
                document.getElementById('selling_price').value = selectedOption.dataset.sellingPrice;
                document.getElementById('min_level').value = selectedOption.dataset.minLevel;
                
                // Generate new short code for replenishment
                const originalCode = selectedOption.dataset.shortCode;
                const timestamp = new Date().toISOString().slice(5, 10).replace('-', '');
                document.getElementById('short_code').value = originalCode + '-R' + timestamp;
                document.getElementById('code_note').style.display = 'block';
                
                // Update labels and help text
                document.getElementById('stock_label').innerHTML = 'New Stock Quantity * <span class="text-blue-600">(Adding to existing)</span>';
                document.getElementById('stock_help').textContent = 'Enter the quantity you\'re adding (not total stock)';
                document.getElementById('stock_update_reason').value = 'Stock replenishment - ' + new Date().toLocaleDateString();
                document.getElementById('submit_btn').innerHTML = '<i class="fas fa-plus mr-2"></i>Add Replenishment Stock';
                
                // Show current stock info
                const currentStock = selectedOption.dataset.currentStock;
                const minLevel = selectedOption.dataset.minLevel;
                document.getElementById('current_stock_display').innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div><strong>Current Stock:</strong> ${currentStock}</div>
                        <div><strong>Minimum Level:</strong> ${minLevel}</div>
                        <div><strong>Status:</strong> ${currentStock <= minLevel ? '<span class="text-red-600">⚠️ Below Minimum</span>' : '<span class="text-green-600">✅ Above Minimum</span>'}</div>
                        <div><strong>New Code:</strong> ${originalCode}-R${timestamp}</div>
                    </div>
                `;
                document.getElementById('replenishment_info').style.display = 'block';
                
                // Show stock details
                document.getElementById('stock_details').innerHTML = `
                    <div class="mt-2">
                        <strong>Item:</strong> [${originalCode}] ${selectedOption.dataset.name}<br>
                        <strong>Current Stock:</strong> ${currentStock} units<br>
                        <strong>Minimum Level:</strong> ${minLevel} units<br>
                        <strong>Status:</strong> ${currentStock <= minLevel ? '<span class="text-red-600">⚠️ Needs Replenishment</span>' : '<span class="text-green-600">✅ Stock Level OK</span>'}
                    </div>
                `;
                document.getElementById('current_stock_info').style.display = 'block';
                
            } else {
                clearForm();
            }
        });

        function clearForm() {
            // Reset form title and button
            document.getElementById('form_title').innerHTML = '<i class="fas fa-plus mr-3"></i>Add New Inventory Item';
            document.getElementById('clear_form').style.display = 'none';
            document.getElementById('submit_btn').innerHTML = '<i class="fas fa-plus mr-2"></i>Add Inventory Item';
            
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

        // New procurement functions
        function createPOForSelected() {
            const select = document.getElementById('existing_item_select');
            if (!select.value) {
                alert('Please select an item first.');
                return;
            }
            
            const selectedOption = select.options[select.selectedIndex];
            const itemData = {
                name: selectedOption.dataset.name,
                code: selectedOption.dataset.shortCode,
                supplier: selectedOption.dataset.supplier,
                buyingPrice: selectedOption.dataset.buyingPrice,
                currentStock: selectedOption.dataset.currentStock,
                minLevel: selectedOption.dataset.minLevel
            };
            
            // Redirect to PO creation with pre-filled data
            const params = new URLSearchParams(itemData);
            window.location.href = `{{ route('purchase-orders.create') }}?${params}`;
        }

        function createPOFromStock() {
            window.location.href = `{{ route('purchase-orders.create') }}?low_stock=1`;
        }

        function showLowStockItems() {
            document.getElementById('lowStockModal').classList.remove('hidden');
            
            // Load low stock items via AJAX (placeholder for now)
            setTimeout(() => {
                document.getElementById('lowStockContent').innerHTML = `
                    <div class="text-center text-gray-500">
                        <i class="fas fa-check-circle text-6xl mb-4 text-green-500"></i>
                        <h5 class="text-lg font-bold mb-2">All Stock Levels OK</h5>
                        <p>No items are currently below minimum level.</p>
                    </div>
                `;
            }, 1000);
        }

        function createPOForLowStock() {
            window.location.href = `{{ route('purchase-orders.create') }}?low_stock=1`;
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function scrollToInventoryForm() {
            document.getElementById('inventory-form-section').scrollIntoView({ behavior: 'smooth' });
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

        // Enhanced validation for employee form
        document.getElementById('telephone').addEventListener('input', function() {
            const value = this.value;
            const regex = /^\+?[0-9]{7,20}$/;
            const errorSpan = document.getElementById('telephone-error');
            
            if (value && !regex.test(value)) {
                errorSpan.textContent = '⚠️ Enter a valid telephone number (e.g. +27721234567)';
                this.classList.add('border-red-500');
            } else if (value) {
                errorSpan.textContent = '✅ Valid telephone number';
                errorSpan.classList.remove('text-red-500');
                errorSpan.classList.add('text-green-500');
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } else {
                errorSpan.textContent = '';
                this.classList.remove('border-red-500', 'border-green-500');
            }
        });

        document.getElementById('email').addEventListener('input', function() {
            const value = this.value;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const errorSpan = document.getElementById('email-error');
            
            if (value && !regex.test(value)) {
                errorSpan.textContent = '⚠️ Enter a valid email address';
                this.classList.add('border-red-500');
            } else if (value) {
                errorSpan.textContent = '✅ Valid email address';
                errorSpan.classList.remove('text-red-500');
                errorSpan.classList.add('text-green-500');
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } else {
                errorSpan.textContent = '';
                this.classList.remove('border-red-500', 'border-green-500');
            }
        });
    </script>
</body>
</html>