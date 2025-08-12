<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .card-hover:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
        }
        
        .gradient-procurement {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-stock {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .gradient-employee {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .gradient-company {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .admin-level-0 { background-color: #f3f4f6; color: #1f2937; }
        .admin-level-1 { background-color: #fef3c7; color: #92400e; }
        .admin-level-2 { background-color: #d1fae5; color: #065f46; }
        .admin-level-3 { background-color: #dbeafe; color: #1e40af; }
        .admin-level-4 { background-color: #e9d5ff; color: #7c2d12; }
        .admin-level-5 { background-color: #fecaca; color: #991b1b; }

        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <!-- DEBUG INFORMATION -->
    @if(isset($debug))
    <div class="alert alert-info m-3">
        <h5>🔍 Debug Information:</h5>
        <p><strong>Before DB:</strong> {{ $debug['before_db'] ?? 'Not set' }}</p>
        <p><strong>After DB:</strong> {{ $debug['after_db'] ?? 'Not set' }}</p>
        <p><strong>Tenant Session:</strong> {{ $debug['tenant_session'] ?? 'Not set' }}</p>
        <p><strong>Current User:</strong> {{ $debug['current_user'] ?? 'Not set' }}</p>
        <p><strong>Current User ID:</strong> {{ $debug['current_user_id'] ?? 'Not set' }}</p>
        <p><strong>Auth Guard:</strong> {{ $debug['auth_guard'] ?? 'Not set' }}</p>
        <p><strong>Users Count:</strong> {{ $users->count() ?? 0 }}</p>
        <p><strong>Employees Count:</strong> {{ $employees->count() ?? 0 }}</p>
        <p><strong>GRVs Count:</strong> {{ $debug['grvs_count'] ?? 0 }}</p>
        <p><strong>Actual DB Query:</strong> {{ $debug['actual_db_query'] ?? 'Not set' }}</p>
        <p><strong>Users Emails:</strong> {{ implode(', ', $debug['users_emails'] ?? []) }}</p>
        <p><strong>Employees Emails:</strong> {{ implode(', ', $debug['employees_emails'] ?? []) }}</p>
    </div>
    @endif
    
    <!-- Navigation Bar -->
  
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-tools me-2"></i>Master Settings
            </span>
            <div class="navbar-nav ms-auto">
                @if(auth()->user()->canManageUsers())
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="fas fa-users me-1"></i>Users
                    </a>
                @endif
                <a href="{{ route('inventory.index') }}" class="nav-link">
                    <i class="fas fa-boxes me-1"></i>Inventory
                </a>
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

  

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="text-dark fw-bold mb-1">
                    <i class="fas fa-tools text-primary me-2"></i>
                    Master Settings & Management
                </h2>
                <p class="text-muted">Comprehensive system management and inventory control</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-boxes me-1"></i>Inventory
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif 

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Quick Action Dashboard --}}
        <div class="row g-4 mb-4">
            {{-- Purchase Orders Card --}}
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm card-hover">
                    <div class="gradient-procurement text-white p-4 text-center">
                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                        <h5 class="fw-bold">Purchase Orders</h5>
                        <p class="small opacity-90 mb-0">Create & manage purchase orders</p>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-grid gap-2 mt-auto">
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-list me-1"></i>View All POs
                            </a>
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Create New PO
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Suppliers Card --}}
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm card-hover">
                    <div class="bg-success text-white p-4 text-center">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <h5 class="fw-bold">Suppliers</h5>
                        <p class="small opacity-90 mb-0">Manage supplier information</p>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-grid gap-2 mt-auto">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-users me-1"></i>View Suppliers
                            </a>
                            <a href="{{ route('suppliers.create') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-user-plus me-1"></i>Add Supplier
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GRV System Card --}}
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm card-hover">
                    <div class="bg-warning text-dark p-4 text-center">
                        <i class="fas fa-truck-loading fa-3x mb-3"></i>
                        <h5 class="fw-bold">GRV System</h5>
                        <p class="small opacity-75 mb-0">Goods received vouchers</p>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-grid gap-2 mt-auto">
                            <a href="{{ route('grv.index') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-clipboard-check me-1"></i>View GRVs
                            </a>
                            <button class="btn btn-outline-warning btn-sm" onclick="showLowStockItems()">
                                <i class="fas fa-exclamation-triangle me-1"></i>Check Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory Card --}}
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm card-hover">
                    <div class="bg-info text-white p-4 text-center">
                        <i class="fas fa-boxes fa-3x mb-3"></i>
                        <h5 class="fw-bold">Inventory</h5>
                        <p class="small opacity-90 mb-0">View and manage stock</p>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-grid gap-2 mt-auto">
                            <a href="{{ route('inventory.index') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-warehouse me-1"></i>View Inventory
                            </a>
                            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle me-1"></i>New Inventory Item
                            </a>
                            <button class="btn btn-outline-info btn-sm" onclick="scrollToInventoryForm()">
                                <i class="fas fa-plus me-1"></i>Quick Add/Replenish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        {{-- User Management Section --}}
            <div class="card mb-4 shadow-sm">
            <div class="gradient-employee text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h4 fw-bold mb-1">
                            <i class="fas fa-users me-2"></i>User & Employee Management
                        </h3>
                        <p class="mb-0 opacity-90">Add new employees and manage user permissions</p>
                    </div>
                   
                </div>
            </div>
            
           
            
           <form action="{{ tenant() ? route('settings.store') : route('master_settings.store') }}" method="POST" id="userEmployeeForm">
    @csrf
    <div class="row g-3">
        {{-- Type Selection Dropdown --}}
        <div class="col-md-12">
            <label class="form-label fw-bold">Account Type *</label>
            <select name="account_type" id="account_type" class="form-select" required>
                <option value="">-- Select Account Type --</option>
                <option value="user">User (System Access)</option>
                <option value="employee">Employee (Staff Record)</option>
            </select>
            <small class="text-muted">Choose "User" for system access accounts or "Employee" for staff records</small>
        </div>

        {{-- Name Fields --}}
        <div class="col-md-4">
            <label class="form-label">First Name *</label>
            <input type="text" name="name" id="name" class="form-control" required>
            <div class="invalid-feedback">First name is required.</div>
        </div>
        <div class="col-md-4" id="surname_field" style="display: none;">
            <label class="form-label">Surname *</label>
            <input type="text" name="surname" id="surname" class="form-control">
            <div class="invalid-feedback">Surname is required for employees.</div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" id="email" class="form-control" required>
            <div class="invalid-feedback" id="emailFeedback">Please enter a valid email address.</div>
        </div>

        {{-- Role --}}
        <div class="col-md-4">
            <label class="form-label">Role *</label>
            <select name="role" id="role" class="form-select" required>
                <option value="">-- Select Role --</option>
                <option value="admin">Administrator</option>
                <option value="manager">Manager</option>
                <option value="supervisor">Supervisor</option>
                <option value="artisan">Artisan</option>
                <option value="staff">Staff Member</option>
                <option value="user">User</option>
            </select>
            <div class="invalid-feedback">Role is required.</div>
        </div>

        {{-- Admin Level --}}
        <div class="col-md-3">
            <label class="form-label">Admin Level</label>
            <select name="admin_level" id="admin_level" class="form-select">
                <option value="0">No Admin Rights</option>
                <option value="1">Basic Access</option>
                <option value="2">Company Settings</option>
                <option value="3">User Management</option>
                <option value="4">System Admin</option>
                <option value="5">Master Admin</option>
            </select>
        </div>

        {{-- User ID (for users) --}}
        <div class="col-md-3" id="user_id_field" style="display: none;">
            <label class="form-label">User ID</label>
            <input type="text" name="user_id" id="user_id" class="form-control" readonly>
        </div>

        {{-- Employee ID (for employees) --}}
        <div class="col-md-3" id="employee_id_field" style="display: none;">
            <label class="form-label">Employee ID</label>
            <input type="text" name="employee_id" id="employee_id" class="form-control" readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">Department</label>
            <input type="text" name="department" id="department" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Position</label>
            <input type="text" name="position" id="position" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="telephone" id="telephone" class="form-control" placeholder="+27 XX XXX XXXX">
            <div class="invalid-feedback" id="phoneFeedback">Please enter a valid phone number (10-15 digits, may start with +).</div>
        </div>
        <div class="col-md-4">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="bypass_verification" id="bypass_verification" value="1">
                <label class="form-check-label" for="bypass_verification">
                    Skip verification (Testing)
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password *</label>
            <input type="password" name="password" id="password" class="form-control" required minlength="8" autocomplete="new-password">
            <div class="invalid-feedback" id="passwordFeedback">
                Password must be at least 8 characters, contain uppercase, lowercase, number, and special character.
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
            <div class="invalid-feedback" id="confirmPasswordFeedback">
                Passwords do not match.
            </div>
        </div>
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-success" disabled id="submitBtn">
                <i class="fas fa-user-plus me-2"></i><span id="submitText">Add User/Employee</span>
            </button>
        </div>
    </div>
</form>
        {{-- Company Management Section --}}
        <div class="card mb-4 shadow-sm">
            <div class="gradient-company text-dark p-4">
                <h3 class="h4 fw-bold mb-1">
                    <i class="fas fa-building me-2"></i>Company Management
                </h3>
                <p class="mb-0 opacity-75">Update your company information, contact details, and business settings</p>
            </div>
            <div class="card-body text-center">
                <p class="text-muted mb-4">
                    Manage your company profile, contact information, banking details, and business preferences.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('company.details') }}" class="btn btn-primary">
                        <i class="fas fa-building me-2"></i>Edit Company Details
                    </a>
                    <button class="btn btn-outline-primary" onclick="checkCompanySetup()">
                        <i class="fas fa-check-circle me-2"></i>Verify Setup
                    </button>
                </div>
            </div>
        </div>

        {{-- Inventory Form Section --}}
        <div class="card mb-4 shadow-sm" id="inventory-form-section">
            <div class="gradient-stock text-white p-4">
                <h3 class="h4 fw-bold mb-1">
                    <i class="fas fa-boxes me-2"></i>Inventory Management
                </h3>
                <p class="mb-0 opacity-90">Add new inventory items or replenish existing stock</p>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.store') }}" method="POST" id="inventory_form">
                    @csrf
                    
                    {{-- Hidden tracking fields --}}
                    <input type="hidden" id="is_replenishment" name="is_replenishment" value="0">
                    <input type="hidden" id="original_item_id" name="original_item_id" value="">
                    <input type="hidden" id="nett_price" name="nett_price">
                    <input type="hidden" id="sell_price" name="sell_price">
                    <input type="hidden" id="quantity" name="quantity">
                    <input type="hidden" id="min_quantity" name="min_quantity">
                    <input type="hidden" id="stock_added" name="stock_added">
                    <input type="hidden" id="last_stock_update" name="last_stock_update">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 id="form_title">
                                <i class="fas fa-plus me-3"></i>Add New Inventory Item
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" id="clear_form" class="btn btn-outline-secondary" onclick="clearForm()" style="display: none;">
                                <i class="fas fa-refresh me-1"></i>Clear Form
                            </button>
                        </div>
                    </div>

                    {{-- Existing Item Selection --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                <i class="fas fa-search me-1"></i>Replenish Existing Item (Optional)
                            </label>
                            <select id="existing_item_select" class="form-select">
                                <option value="">-- Select existing item to replenish --</option>
                                @if(isset($inventory) && $inventory->count() > 0)
                                    @foreach($inventory as $item)
                                        <option value="{{ $item->id }}" 
                                                data-name="{{ $item->name }}"
                                                data-short-code="{{ $item->short_code }}"
                                                data-vendor="{{ $item->vendor }}"
                                                data-supplier="{{ $item->supplier }}"
                                                data-buying-price="{{ $item->buying_price }}"
                                                data-selling-price="{{ $item->selling_price }}"
                                                data-current-stock="{{ $item->stock_level }}"
                                                data-min-level="{{ $item->min_level }}">
                                            [{{ $item->short_code }}] {{ $item->name }} (Current: {{ $item->stock_level }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Select an item above to replenish stock, or leave blank to add a new item</small>
                        </div>
                    </div>

                    {{-- Current Stock Info (Hidden by default) --}}
                    <div id="current_stock_info" class="alert alert-info" style="display: none;">
                        <h6><i class="fas fa-info-circle me-2"></i>Current Stock Information</h6>
                        <div id="current_stock_display"></div>
                    </div>

                    {{-- Replenishment Info (Hidden by default) --}}
                    <div id="replenishment_info" class="alert alert-warning" style="display: none;">
                        <h6><i class="fas fa-plus-circle me-2"></i>Stock Replenishment</h6>
                        <div id="stock_details"></div>
                        <div id="code_note" style="display: none;">
                            <small class="text-info">
                                <i class="fas fa-info-circle me-1"></i>
                                A new replenishment code will be generated automatically
                            </small>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Item Name *</label>
                            <input type="text" id="item_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Short Code *</label>
                            <input type="text" id="short_code" name="short_code" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vendor</label>
                            <input type="text" id="vendor" name="vendor" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Supplier</label>
                            <input type="text" id="supplier" name="supplier" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Purchase Date</label>
                            <input type="date" id="purchase_date" name="purchase_date" class
                        </div>
                        
                        <div class="col-md-3">
                            <label id="stock_label" class="form-label fw-bold">Stock Level *</label>
                            <input type="number" id="stock_level" name="stock_level" class="form-control" min="0" step="0.01" required>
                            <small id="stock_help" class="text-muted">Enter the quantity you're adding to inventory</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Minimum Level</label>
                            <input type="number" id="min_level" name="min_level" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Buying Price</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" id="buying_price" name="buying_price" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" id="selling_price" name="selling_price" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Stock Update Reason</label>
                            <input type="text" id="stock_update_reason" name="stock_update_reason" class="form-control" value="Initial stock entry">
                        </div>
                        
                        <div class="col-12 text-end">
                            <button type="submit" id="submit_btn" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Add Inventory Item
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Low Stock Modal --}}
    <div class="modal fade" id="lowStockModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Low Stock Items
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="lowStockContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Checking stock levels...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const accountType = document.getElementById('account_type');
    const surnameField = document.getElementById('surname_field');
    const surnameInput = document.getElementById('surname');
    const userIdField = document.getElementById('user_id_field');
    const userIdInput = document.getElementById('user_id');
    const employeeIdField = document.getElementById('employee_id_field');
    const employeeIdInput = document.getElementById('employee_id');
    const roleInput = document.getElementById('role');
    const submitBtn = document.getElementById('submitBtn');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('telephone');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    // Helper: Generate Employee ID
    function generateEmployeeId() {
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const random = Math.floor(1000 + Math.random() * 9000);
        return `EMP${yyyy}${mm}${dd}-${random}`;
    }
    // Helper: Generate User ID (role-based, no prefix)
    function generateUserId(role) {
        if (!role) return '';
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const random = Math.floor(1000 + Math.random() * 9000);
        return `${role.toUpperCase().replace(/\s+/g, '')}-${yyyy}${mm}${dd}-${random}`;
    }

    // Show/hide fields and generate IDs
    function updateFormFields() {
        if (accountType.value === 'employee') {
            surnameField.style.display = '';
            surnameInput.required = true;
            employeeIdField.style.display = '';
            userIdField.style.display = 'none';
            employeeIdInput.value = generateEmployeeId();
            userIdInput.value = '';
        } else if (accountType.value === 'user') {
            surnameField.style.display = 'none';
            surnameInput.required = false;
            employeeIdField.style.display = 'none';
            userIdField.style.display = '';
            userIdInput.value = generateUserId(roleInput.value);
            employeeIdInput.value = '';
        } else {
            surnameField.style.display = 'none';
            surnameInput.required = false;
            employeeIdField.style.display = 'none';
            userIdField.style.display = 'none';
            employeeIdInput.value = '';
            userIdInput.value = '';
        }
    }
    accountType.addEventListener('change', updateFormFields);
    roleInput.addEventListener('change', function() {
        if (accountType.value === 'user') {
            userIdInput.value = generateUserId(roleInput.value);
        }
    });

    // Real-time validation
    function validate() {
        let valid = true;

        // Name
        if (!nameInput.value.trim()) {
            nameInput.classList.add('is-invalid');
            valid = false;
        } else {
            nameInput.classList.remove('is-invalid');
        }

        // Surname (if employee)
        if (accountType.value === 'employee' && !surnameInput.value.trim()) {
            surnameInput.classList.add('is-invalid');
            valid = false;
        } else {
            surnameInput.classList.remove('is-invalid');
        }

        // Email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value)) {
            emailInput.classList.add('is-invalid');
            valid = false;
        } else {
            emailInput.classList.remove('is-invalid');
        }

        // Role
        if (!roleInput.value) {
            roleInput.classList.add('is-invalid');
            valid = false;
        } else {
            roleInput.classList.remove('is-invalid');
        }

        // Phone
        const phonePattern = /^\+?\d{10,15}$/;
        if (phoneInput.value && !phonePattern.test(phoneInput.value)) {
            phoneInput.classList.add('is-invalid');
            valid = false;
        } else {
            phoneInput.classList.remove('is-invalid');
        }

        // Password
        const passPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!passPattern.test(passwordInput.value)) {
            passwordInput.classList.add('is-invalid');
            valid = false;
        } else {
            passwordInput.classList.remove('is-invalid');
        }

        // Confirm Password
        if (confirmInput.value !== passwordInput.value || !confirmInput.value) {
            confirmInput.classList.add('is-invalid');
            valid = false;
        } else {
            confirmInput.classList.remove('is-invalid');
        }

        submitBtn.disabled = !valid;
    }

    // Attach validation listeners
    [nameInput, surnameInput, emailInput, roleInput, phoneInput, passwordInput, confirmInput, accountType].forEach(el => {
        el.addEventListener('input', validate);
        el.addEventListener('change', validate);
    });

    // Initial state
    updateFormFields();
    validate();
});
</script>