<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
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

        .card-hover:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-users me-2"></i>User Management
            </span>
            <div class="navbar-nav ms-auto">
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
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="text-dark fw-bold mb-1">
                    <i class="fas fa-users text-primary me-2"></i>
                    User Management
                </h2>
                <p class="text-muted">Manage system users, roles, and permissions</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('master.settings') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tools me-1"></i>Master Settings
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus me-1"></i>Add User
                    </button>
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

        {{-- Users Statistics Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">{{ $users->total() ?? 0 }}</h5>
                                <p class="card-text">Total Users</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">{{ $users->where('is_active', true)->count() ?? 0 }}</h5>
                                <p class="card-text">Active Users</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">{{ $users->where('admin_level', '>=', 3)->count() ?? 0 }}</h5>
                                <p class="card-text">Administrators</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">{{ $users->where('is_superuser', true)->count() ?? 0 }}</h5>
                                <p class="card-text">Super Users</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-crown fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>System Users
                </h5>
            </div>
            <div class="card-body p-0">
                @if(isset($users) && $users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Admin Level</th>
                                    <th>Status</th>
                                    <th>Employee Info</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge admin-level-{{ min($user->admin_level, 5) }}">
                                                {{ $user->role_display ?? ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-medium">Level {{ $user->admin_level }}</span>
                                            <div class="text-muted small">
                                                @switch($user->admin_level)
                                                    @case(0) No Admin Rights @break
                                                    @case(1) Basic Access @break
                                                    @case(2) Company Settings @break
                                                    @case(3) User Management @break
                                                    @case(4) System Admin @break
                                                    @case(5) Master Admin @break
                                                    @default Unknown @break
                                                @endswitch
                                            </div>
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
                                            @if($user->employee_id)
                                                <div class="small">
                                                    <strong>ID:</strong> {{ $user->employee_id }}<br>
                                                    @if($user->department)
                                                        <strong>Dept:</strong> {{ $user->department }}<br>
                                                    @endif
                                                    @if($user->position)
                                                        <strong>Position:</strong> {{ $user->position }}
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                {{ $user->created_at->format('M d, Y') }}<br>
                                                <span class="text-muted">{{ $user->created_at->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if(auth()->user()->canManageUsers())
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editUser({{ $user->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if($user->id !== auth()->id())
                                                        <button class="btn btn-outline-warning btn-sm" onclick="toggleUserStatus({{ $user->id }})">
                                                            <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                        @if(!$user->is_superuser || auth()->user()->is_superuser)
                                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteUser({{ $user->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($users->hasPages())
                        <div class="card-footer">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>No Users Found</h5>
                        <p>There are no users in the system yet.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus me-1"></i>Add First User
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add User Modal --}}
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
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
                            <div class="col-md-6">
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
                            <div class="col-md-4">
                                <label class="form-label">Employee ID</label>
                                <input type="text" name="employee_id" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Leave blank for default password">
                                <small class="text-muted">If left blank, default password "password" will be used</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i>Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role *</label>
                                <select name="role" id="edit_role" class="form-select" required>
                                    <option value="admin">Administrator</option>
                                    <option value="manager">Manager</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="artisan">Artisan</option>
                                    <option value="staff">Staff Member</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admin Level</label>
                                <select name="admin_level" id="edit_admin_level" class="form-select">
                                    <option value="0">No Admin Rights</option>
                                    <option value="1">Basic Access</option>
                                    <option value="2">Company Settings</option>
                                    <option value="3">User Management</option>
                                    <option value="4">System Admin</option>
                                    <option value="5">Master Admin</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Employee ID</label>
                                <input type="text" name="employee_id" id="edit_employee_id" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" id="edit_department" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" id="edit_position" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                    <label class="form-check-label" for="edit_is_active">
                                        User is active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Edit User Function
        function editUser(userId) {
            fetch(`/users/${userId}`)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('edit_name').value = user.name;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_admin_level').value = user.admin_level;
                    document.getElementById('edit_employee_id').value = user.employee_id || '';
                    document.getElementById('edit_department').value = user.department || '';
                    document.getElementById('edit_position').value = user.position || '';
                    document.getElementById('edit_is_active').checked = user.is_active;
                    
                    document.getElementById('editUserForm').action = `/users/${userId}`;
                    
                    new bootstrap.Modal(document.getElementById('editUserModal')).show();
                })
                .catch(error => {
                    alert('Error loading user data');
                    console.error('Error:', error);
                });
        }

        // Toggle User Status
        function toggleUserStatus(userId) {
            if (confirm('Are you sure you want to toggle this user\'s status?')) {
                fetch(`/users/${userId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating user status');
                    }
                })
                .catch(error => {
                    alert('Error updating user status');
                    console.error('Error:', error);
                });
            }
        }

        // Delete User
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch(`/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting user');
                    }
                })
                .catch(error => {
                    alert('Error deleting user');
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>