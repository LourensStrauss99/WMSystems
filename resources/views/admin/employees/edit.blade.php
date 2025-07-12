{{-- filepath: resources/views/admin/employees/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-user-edit me-2 text-primary"></i>Edit Employee
        </h2>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to User Management
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label class="form-label fw-bold">First Name</label>
                    <input type="text" name="name" value="{{ $employee->name }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Surname</label>
                    <input type="text" name="surname" value="{{ $employee->surname }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Telephone</label>
                    <input type="text" name="telephone" value="{{ $employee->telephone }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" value="{{ $employee->email }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ $employee->role == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="supervisor" {{ $employee->role == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="artisan" {{ $employee->role == 'artisan' ? 'selected' : '' }}>Artisan</option>
                        <option value="staff" {{ $employee->role == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="user" {{ $employee->role == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Admin Level</label>
                    <input type="number" name="admin_level" value="{{ $employee->admin_level }}" class="form-control" min="0" max="5">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Employee ID</label>
                    <input type="text" name="employee_id" value="{{ $employee->employee_id }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Department</label>
                    <input type="text" name="department" value="{{ $employee->department }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Position</label>
                    <input type="text" name="position" value="{{ $employee->position }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $employee->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$employee->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Super User</label>
                    <select name="is_superuser" class="form-select">
                        <option value="1" {{ $employee->is_superuser ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$employee->is_superuser ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Created By</label>
                    <input type="text" name="created_by" value="{{ $employee->created_by }}" class="form-control" readonly>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection