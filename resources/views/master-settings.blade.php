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
                            <button class="btn btn-outline-info btn-sm" onclick="scrollToInventoryForm()">
                                <i class="fas fa-plus me-1"></i>Add Items
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Procurement Quick Actions --}}
        <div class="card mb-4 shadow-sm">
            <div class="gradient-procurement text-white p-4">
                <h3 class="h4 fw-bold mb-1">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Procurement Quick Actions
                </h3>
                <p class="mb-0 opacity-90">Fast access to procurement management tools</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <button onclick="createPOFromStock()" class="btn btn-primary w-100 h-100 p-4 text-start">
                            <i class="fas fa-magic fa-2x mb-2 d-block"></i>
                            <div class="fw-bold">Quick PO from Stock</div>
                            <small class="opacity-75">Create PO for low stock items</small>
                        </button>
                    </div>
                    
                    <div class="col-md-4">
                        <button onclick="showLowStockItems()" class="btn btn-warning w-100 h-100 p-4 text-start">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                            <div class="fw-bold">Low Stock Alert</div>
                            <small class="opacity-75">View items needing replenishment</small>
                        </button>
                    </div>
                    
                    <div class="col-md-4">
                        <button onclick="createPOForSelected()" class="btn btn-success w-100 h-100 p-4 text-start">
                            <i class="fas fa-file-plus fa-2x mb-2 d-block"></i>
                            <div class="fw-bold">PO for Selected Item</div>
                            <small class="opacity-75">Create PO for chosen inventory</small>
                        </button>
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
                    @if(auth()->user()->canManageUsers())
                        <div>
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Full User Management
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Current Users/Employees Table --}}
            <div class="card-body border-bottom">
                <h5 class="fw-semibold mb-3">
                    <i class="fas fa-list me-2 text-primary"></i>Current Users/Employees
                </h5>
                
                @if((isset($users) && $users->count() > 0) || (isset($employees) && $employees->count() > 0))
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>User/Employee</th>
                                    <th>Type</th>
                                    <th>Role</th>
                                    <th>Admin Level</th>
                                    <th>Status</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Display Users --}}
                                @if(isset($users))
                                    @foreach($users as $user)
                                        <tr class="{{ !$user->is_active ? 'table-secondary opacity-75' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold d-flex align-items-center">
                                                            {{ $user->name }}
                                                            @if($user->is_superuser)
                                                                <span class="badge bg-danger ms-2">
                                                                    <i class="fas fa-crown me-1"></i>SUPER USER
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted small">{{ $user->email }}</div>
                                                        @if($user->employee_id)
                                                            <div class="text-muted smaller">ID: {{ $user->employee_id }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-user me-1"></i>User
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge admin-level-{{ min($user->admin_level, 5) }}">
                                                    {{ $user->role_display }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">Level {{ $user->admin_level }}</span>
                                                <div class="text-muted small">{{ $user->admin_level_name }}</div>
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">
                                                    @if($user->canAccessCompanySettings())
                                                        <div class="text-success mb-1">
                                                            <i class="fas fa-building me-1"></i>Company Settings
                                                        </div>
                                                    @endif
                                                    @if($user->canManageUsers())
                                                        <div class="text-primary mb-1">
                                                            <i class="fas fa-users me-1"></i>User Management
                                                        </div>
                                                    @endif
                                                    @if($user->canManagePurchaseOrders())
                                                        <div class="text-info mb-1">
                                                            <i class="fas fa-file-invoice me-1"></i>Purchase Orders
                                                        </div>
                                                    @endif
                                                    @if($user->canManageInventory())
                                                        <div class="text-warning">
                                                            <i class="fas fa-boxes me-1"></i>Inventory
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if(auth()->user()->canManageUsers())
                                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary btn-sm" title="Edit User">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($user->id !== auth()->id())
                                                            <button class="btn btn-outline-warning btn-sm" onclick="toggleUserStatus({{ $user->id }})" title="Toggle Status">
                                                                <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                                            </button>
                                                            @if(!$user->is_superuser || auth()->user()->is_superuser)
                                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteUser({{ $user->id }})" title="Delete User">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                {{-- Display Employees --}}
                                @if(isset($employees))
                                    @foreach($employees as $employee)
                                        <tr class="{{ !$employee->is_active ? 'table-secondary opacity-75' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3" style="background: #28a745;">
                                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold d-flex align-items-center">
                                                            {{ $employee->name }} {{ $employee->surname }}
                                                            @if($employee->is_superuser)
                                                                <span class="badge bg-danger ms-2">
                                                                    <i class="fas fa-crown me-1"></i>SUPER USER
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted small">{{ $employee->email }}</div>
                                                        @if($employee->employee_id)
                                                            <div class="text-muted smaller">ID: {{ $employee->employee_id }}</div>
                                                        @endif
                                                        @if($employee->telephone)
                                                            <div class="text-muted smaller">
                                                                <i class="fas fa-phone me-1"></i>{{ $employee->telephone }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-user-tie me-1"></i>Employee
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge admin-level-{{ min($employee->admin_level, 5) }}">
                                                    {{ $employee->role_display }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">Level {{ $employee->admin_level }}</span>
                                                <div class="text-muted small">{{ $employee->admin_level_name }}</div>
                                            </td>
                                            <td>
                                                @if($employee->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">
                                                    @if($employee->canAccessCompanySettings())
                                                        <div class="text-success mb-1">
                                                            <i class="fas fa-building me-1"></i>Company Settings
                                                        </div>
                                                    @endif
                                                    @if($employee->canManageUsers())
                                                        <div class="text-primary mb-1">
                                                            <i class="fas fa-users me-1"></i>User Management
                                                        </div>
                                                    @endif
                                                    @if($employee->canManagePurchaseOrders())
                                                        <div class="text-info mb-1">
                                                            <i class="fas fa-file-invoice me-1"></i>Purchase Orders
                                                        </div>
                                                    @endif
                                                    @if($employee->canManageInventory())
                                                        <div class="text-warning">
                                                            <i class="fas fa-boxes me-1"></i>Inventory
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if(auth()->user()->canManageUsers())
                                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-success btn-sm" title="Edit Employee">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="btn btn-outline-warning btn-sm" onclick="toggleEmployeeStatus({{ $employee->id }})" title="Toggle Status">
                                                            <i class="fas fa-{{ $employee->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                        @if(!$employee->is_superuser || auth()->user()->is_superuser)
                                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteEmployee({{ $employee->id }})" title="Delete Employee">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>No Users or Employees Found</h5>
                        <p>There are no users or employees in the system yet.</p>
                    </div>
                @endif
            </div>
            
            {{-- Add New User Form (Only if user has permission) --}}
            @if(auth()->user()->canManageUsers())
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">
                        <i class="fas fa-user-plus me-2 text-success"></i>Add New User/Employee
                    </h5>
                    <form action="#" method="POST" id="userEmployeeForm">
                        @csrf
                        <div class="row g-3">
                            {{-- Type Selection Dropdown --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Account Type *</label>
                                <select name="account_type" id="account_type" class="form-select" required onchange="updateFormAction()">
                                    <option value="">-- Select Account Type --</option>
                                    <option value="user">User (System Access)</option>
                                    <option value="employee">Employee (Staff Record)</option>
                                </select>
                                <small class="text-muted">Choose "User" for system access accounts or "Employee" for staff records</small>
                            </div>

                            {{-- Name Fields --}}
                            <div class="col-md-4">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-4" id="surname_field" style="display: none;">
                                <label class="form-label">Surname *</label>
                                <input type="text" name="surname" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            {{-- Rest of your existing fields... --}}
                            <div class="col-md-4">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-select" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="admin">Administrator</option>
                                    <option value="manager">Manager</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="artisan">Artisan</option>
                                    <option value="staff">Staff Member</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admin Level</label>
                                <select name="admin_level" class="form-select">
                                    <option value="0">No Admin Rights</option>
                                    <option value="1">Basic Access</option>
                                    <option value="2">Company Settings</option>
                                    <option value="3">User Management</option>
                                    <option value="4">System Admin</option>
                                    <option value="5">Master Admin</option>
                                </select>
                            </div>
                            
                            {{-- Keep your existing fields --}}
                            <div class="col-md-3">
                                <label class="form-label">Employee ID</label>
                                <input type="text" name="employee_id" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="telephone" class="form-control" placeholder="+27 XX XXX XXXX">
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
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted">User will need this password to sign in</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success" disabled id="submitBtn">
                                    <i class="fas fa-user-plus me-2"></i><span id="submitText">Add User/Employee</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="card-body text-center">
                    <div class="text-muted">
                        <i class="fas fa-user-slash fa-3x mb-3"></i>
                        <h5>Access Restricted</h5>
                        <p>You don't have permission to manage users. Contact an administrator for access.</p>
                    </div>
                </div>
            @endif
        </div>

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
                            <input type="text" id="name" name="name" class="form-control" required>
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
        // Form Management Object
        const FormManager = {
            clearForm() {
                document.getElementById('form_title').innerHTML = '<i class="fas fa-plus me-3"></i>Add New Inventory Item';
                document.getElementById('clear_form').style.display = 'none';
                document.getElementById('submit_btn').innerHTML = '<i class="fas fa-plus me-2"></i>Add Inventory Item';
                
                // Reset hidden tracking fields
                document.getElementById('is_replenishment').value = '0';
                document.getElementById('original_item_id').value = '';
                
                // Reset labels and hide info panels
                document.getElementById('stock_label').textContent = 'Stock Level *';
                document.getElementById('stock_help').textContent = 'Enter the quantity you\'re adding to inventory';
                document.getElementById('code_note').style.display = 'none';
                
                this.hideInfoPanels();
                this.resetForm();
            },

            hideInfoPanels() {
                document.getElementById('current_stock_info').style.display = 'none';
                document.getElementById('replenishment_info').style.display = 'none';
            },

            resetForm() {
                document.getElementById('inventory_form').reset();
                document.getElementById('existing_item_select').value = '';
                document.getElementById('purchase_date').value = new Date().toISOString().split('T')[0];
                document.getElementById('stock_update_reason').value = 'Initial stock entry';
            },

            populateReplenishmentForm(selectedOption) {
                const itemData = this.extractItemData(selectedOption);
                const newCode = this.generateReplenishmentCode(itemData.shortCode);

                // Update form for replenishment
                this.updateFormForReplenishment(itemData, newCode);
                this.populateFormFields(itemData, newCode);
                this.showCurrentStockInfo(itemData, newCode);
            },

            extractItemData(selectedOption) {
                return {
                    id: selectedOption.value,
                    name: selectedOption.dataset.name,
                    shortCode: selectedOption.dataset.shortCode,
                    vendor: selectedOption.dataset.vendor,
                    supplier: selectedOption.dataset.supplier,
                    buyingPrice: selectedOption.dataset.buyingPrice,
                    sellingPrice: selectedOption.dataset.sellingPrice,
                    currentStock: selectedOption.dataset.currentStock,
                    minLevel: selectedOption.dataset.minLevel
                };
            },

            generateReplenishmentCode(originalCode) {
                const timestamp = new Date().toISOString().slice(5, 10).replace('-', '');
                return `${originalCode}-R${timestamp}`;
            },

            updateFormForReplenishment(itemData, newCode) {
                document.getElementById('form_title').innerHTML = `<i class="fas fa-plus me-3"></i>Replenish Stock: ${itemData.name}`;
                document.getElementById('clear_form').style.display = 'inline-block';
                document.getElementById('is_replenishment').value = '1';
                document.getElementById('original_item_id').value = itemData.id;
                document.getElementById('submit_btn').innerHTML = '<i class="fas fa-plus me-2"></i>Add Replenishment Stock';
            },

            populateFormFields(itemData, newCode) {
                const fields = {
                    'name': itemData.name,
                    'vendor': itemData.vendor,
                    'supplier': itemData.supplier,
                    'buying_price': itemData.buyingPrice,
                    'selling_price': itemData.sellingPrice,
                    'min_level': itemData.minLevel,
                    'short_code': newCode,
                    'stock_update_reason': `Stock replenishment - ${new Date().toLocaleDateString()}`
                };

                Object.entries(fields).forEach(([fieldId, value]) => {
                    const element = document.getElementById(fieldId);
                    if (element) element.value = value || '';
                });

                // Update labels and help text
                document.getElementById('stock_label').innerHTML = 'New Stock Quantity * <span class="text-primary">(Adding to existing)</span>';
                document.getElementById('stock_help').textContent = 'Enter the quantity you\'re adding (not total stock)';
                document.getElementById('code_note').style.display = 'block';
            },

            showCurrentStockInfo(itemData, newCode) {
                const isLowStock = itemData.currentStock <= itemData.minLevel;
                const statusHtml = isLowStock 
                    ? '<span class="text-danger">⚠️ Below Minimum</span>' 
                    : '<span class="text-success">✅ Above Minimum</span>';

                document.getElementById('current_stock_display').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-3"><strong>Current Stock:</strong> ${itemData.currentStock}</div>
                        <div class="col-md-3"><strong>Minimum Level:</strong> ${itemData.minLevel}</div>
                        <div class="col-md-3"><strong>Status:</strong> ${statusHtml}</div>
                        <div class="col-md-3"><strong>New Code:</strong> ${newCode}</div>
                    </div>
                `;

                document.getElementById('stock_details').innerHTML = `
                    <div class="mt-2">
                        <strong>Item:</strong> [${itemData.shortCode}] ${itemData.name}<br>
                        <strong>Current Stock:</strong> ${itemData.currentStock} units<br>
                        <strong>Minimum Level:</strong> ${itemData.minLevel} units<br>
                        <strong>Status:</strong> ${isLowStock ? '<span class="text-danger">⚠️ Needs Replenishment</span>' : '<span class="text-success">✅ Stock Level OK</span>'}
                    </div>
                `;

                document.getElementById('current_stock_info').style.display = 'block';
                document.getElementById('replenishment_info').style.display = 'block';
            }
        };

        // Global Functions
        function scrollToInventoryForm() {
            document.getElementById('inventory-form-section').scrollIntoView({ 
                behavior: 'smooth' 
            });
        }

        function createPOFromStock() {
            window.location.href = `{{ route('purchase-orders.create') }}?low_stock=1`;
        }

        function createPOForSelected() {
            const select = document.getElementById('existing_item_select');
            if (!select.value) {
                alert('Please select an item first.');
                return;
            }
            
            const selectedOption = select.options[select.selectedIndex];
            const itemData = FormManager.extractItemData(selectedOption);
            
            const params = new URLSearchParams(itemData);
            window.location.href = `{{ route('purchase-orders.create') }}?${params}`;
        }

        function showLowStockItems() {
            const modal = new bootstrap.Modal(document.getElementById('lowStockModal'));
            modal.show();
            
            // Simulate loading low stock items
            setTimeout(() => {
                document.getElementById('lowStockContent').innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 fw-bold">All Stock Levels OK</h5>
                        <p>No items are currently below minimum level.</p>
                    </div>
                `;
            }, 1000);
        }

        function checkCompanySetup() {
            fetch('{{ route("company.check-setup") }}')
                .then(response => response.json())
                .then(data => {
                    const message = data.complete 
                        ? 'Company setup is complete!' 
                        : 'Company setup needs attention.';
                    alert(message);
                })
                .catch(error => {
                    alert('Error checking company setup.');
                });
        }

        function clearForm() {
            FormManager.clearForm();
        }

        function editUser(userId) {
            alert('Edit user functionality coming soon...');
        }

        function toggleUserStatus(userId) {
            if (confirm('Are you sure you want to toggle this user\'s status?')) {
                alert('Toggle user status functionality coming soon...');
            }
        }

        // Toggle Employee Status
        function toggleEmployeeStatus(employeeId) {
            if (confirm('Are you sure you want to toggle this employee\'s status?')) {
                fetch(`/employees/${employeeId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('danger', data.message || 'Error updating employee status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Error updating employee status');
                });
            }
        }

        // Delete Employee
        function deleteEmployee(employeeId) {
            if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
                fetch(`/employees/${employeeId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('danger', data.message || 'Error deleting employee');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Error deleting employee');
                });
            }
        }

        // Update the existing showAlert function if it doesn't exist
        function showAlert(type, message) {
            const alertContainer = document.querySelector('.container-fluid');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert at the top of the container
            alertContainer.insertBefore(alertDiv, alertContainer.firstChild);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Update form action based on account type selection
        function updateFormAction() {
            const accountType = document.getElementById('account_type').value;
            const form = document.getElementById('userEmployeeForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const surnameField = document.getElementById('surname_field');
            const surnameInput = document.querySelector('input[name="surname"]');

            if (accountType === 'user') {
                form.action = '{{ route("users.store") }}';
                submitBtn.disabled = false;
                submitText.textContent = 'Add User';
                surnameField.style.display = 'none';
                surnameInput.required = false;
            } else if (accountType === 'employee') {
                form.action = '{{ route("employees.store") }}'; // Fixed route
                submitBtn.disabled = false;
                submitText.textContent = 'Add Employee';
                surnameField.style.display = 'block';
                surnameInput.required = true;
            } else {
                form.action = '#';
                submitBtn.disabled = true;
                submitText.textContent = 'Add User/Employee';
                surnameField.style.display = 'none';
                surnameInput.required = false;
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Existing item selection
            const existingItemSelect = document.getElementById('existing_item_select');
            if (existingItemSelect) {
                existingItemSelect.addEventListener('change', function() {
                    if (this.value) {
                        FormManager.populateReplenishmentForm(this.options[this.selectedIndex]);
                    } else {
                        FormManager.clearForm();
                    }
                });
            }

            // Form submission
            const inventoryForm = document.getElementById('inventory_form');
            if (inventoryForm) {
                inventoryForm.addEventListener('submit', function(e) {
                    // Set derived prices
                    document.getElementById('nett_price').value = document.getElementById('buying_price').value;
                    document.getElementById('min_quantity').value = document.getElementById('min_level').value;
                    document.getElementById('stock_added').value = stockLevel;
                    document.getElementById('last_stock_update').value = new Date().toISOString().split('T')[0];
                });
            }
        });
    </script>
</body>
</html>