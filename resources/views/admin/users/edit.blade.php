<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit User: {{ $user->name }} - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-user-edit me-2"></i>Edit User
            </span>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="fas fa-users me-1"></i>All Users
                </a>
                <a href="{{ route('master.settings') }}" class="nav-link">
                    <i class="fas fa-tools me-1"></i>Master Settings
                </a>
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
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

        {{-- User Profile Header --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="mb-1 d-flex align-items-center">
                            {{ $user->name }}
                            @if($user->is_superuser)
                                <span class="badge bg-danger ms-2">
                                    <i class="fas fa-crown me-1"></i>SUPER USER
                                </span>
                            @endif
                        </h3>
                        <p class="text-muted mb-1">{{ $user->email }}</p>
                        <div class="d-flex gap-3">
                            <span class="status-badge admin-level-{{ min($user->admin_level, 5) }}">
                                {{ $user->role_display }}
                            </span>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <small class="text-muted">
                                Admin Level {{ $user->admin_level }} - {{ $user->admin_level_name }}
                            </small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-end">
                            <small class="text-muted">Created: {{ $user->created_at->format('M d, Y') }}</small><br>
                            @if($user->employee_id)
                                <small class="text-muted">Employee ID: {{ $user->employee_id }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Edit User Form --}}
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Edit User Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Role *</label>
                                    <select name="role" class="form-select" required>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="supervisor" {{ $user->role === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="artisan" {{ $user->role === 'artisan' ? 'selected' : '' }}>Artisan</option>
                                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff Member</option>
                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Admin Level</label>
                                    <select name="admin_level" class="form-select" {{ !auth()->user()->canManageUsers() || (!auth()->user()->isSuperAdmin() && $user->admin_level >= auth()->user()->admin_level) ? 'disabled' : '' }}>
                                        <option value="0" {{ $user->admin_level == 0 ? 'selected' : '' }}>No Admin Rights</option>
                                        <option value="1" {{ $user->admin_level == 1 ? 'selected' : '' }}>Basic Access</option>
                                        <option value="2" {{ $user->admin_level == 2 ? 'selected' : '' }}>Company Settings</option>
                                        @if(auth()->user()->admin_level >= 3 || auth()->user()->isSuperAdmin())
                                            <option value="3" {{ $user->admin_level == 3 ? 'selected' : '' }}>User Management</option>
                                        @endif
                                        @if(auth()->user()->admin_level >= 4 || auth()->user()->isSuperAdmin())
                                            <option value="4" {{ $user->admin_level == 4 ? 'selected' : '' }}>System Admin</option>
                                        @endif
                                        @if(auth()->user()->isSuperAdmin())
                                            <option value="5" {{ $user->admin_level == 5 ? 'selected' : '' }}>Master Admin</option>
                                        @endif
                                    </select>
                                    @if(!auth()->user()->canManageUsers() || (!auth()->user()->isSuperAdmin() && $user->admin_level >= auth()->user()->admin_level))
                                        <input type="hidden" name="admin_level" value="{{ $user->admin_level }}">
                                        <small class="text-muted">You cannot modify this user's admin level</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Employee ID</label>
                                    <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $user->employee_id) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Position</label>
                                    <input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            User is active
                                        </label>
                                        @if($user->id === auth()->id())
                                            <input type="hidden" name="is_active" value="1">
                                            <small class="text-muted d-block">You cannot deactivate your own account</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Password Change Card --}}
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-key me-2"></i>Change Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.change-password', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label class="form-label">New Password *</label>
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Confirm New Password *</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-1"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- User Permissions Card --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Current Permissions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            @if($user->canAccessCompanySettings())
                                <div class="d-flex align-items-center text-success mb-2">
                                    <i class="fas fa-check-circle me-2"></i>Company Settings
                                </div>
                            @endif
                            @if($user->canManageUsers())
                                <div class="d-flex align-items-center text-primary mb-2">
                                    <i class="fas fa-check-circle me-2"></i>User Management
                                </div>
                            @endif
                            @if($user->canManagePurchaseOrders())
                                <div class="d-flex align-items-center text-info mb-2">
                                    <i class="fas fa-check-circle me-2"></i>Purchase Orders
                                </div>
                            @endif
                            @if($user->canManageInventory())
                                <div class="d-flex align-items-center text-warning mb-2">
                                    <i class="fas fa-check-circle me-2"></i>Inventory Management
                                </div>
                            @endif
                            @if(!$user->canAccessCompanySettings() && !$user->canManageUsers() && !$user->canManagePurchaseOrders() && !$user->canManageInventory())
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-2"></i>Basic user access only
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>