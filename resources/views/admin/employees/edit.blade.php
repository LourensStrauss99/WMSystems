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
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-2"><i class="fas fa-link text-primary me-2"></i>Share Mobile Login Link</h5>
<<<<<<< HEAD
            <div class="input-group mb-2">
                <input type="text" id="mobile-login-link" class="form-control" readonly value="{{ url('/mobile-app/login?email=' . urlencode($employee->email)) }}">
                <button class="btn btn-outline-secondary" type="button" id="copy-button" onclick="copyMobileLink()">
                    <i class="fas fa-copy me-1"></i>Copy
                </button>
            </div>
            <div id="copy-success-message" class="alert alert-success d-none" role="alert">
                <i class="fas fa-check-circle me-2"></i>Link copied successfully!
=======
            <div class="input-group">
                <!-- Removed tenant logic from mobile login link -->
                <button class="btn btn-outline-secondary" type="button" id="copy-link-btn">Copy</button>
>>>>>>> bf4f09e2d0fd51ad4360c6e9912471a0fe5dc319
            </div>
            <small class="text-muted">Send this link to the employee. It will pre-fill their email on the mobile login page.</small>
            <div class="text-center mt-3">
                <!-- Removed tenant logic from QR code generation -->
                <div class="small text-muted mt-1">Scan to open mobile login link</div>
            </div>
        </div>
    </div>
</div>

<script>
<<<<<<< HEAD
function copyMobileLink() {
    const linkInput = document.getElementById('mobile-login-link');
    const copyButton = document.getElementById('copy-button');
    const successMessage = document.getElementById('copy-success-message');
    
    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(linkInput.value).then(function() {
            showCopySuccess(copyButton, successMessage);
        }).catch(function() {
            // Fallback to older method
            fallbackCopyTextToClipboard(linkInput.value, copyButton, successMessage);
        });
    } else {
        // Fallback for older browsers or non-secure contexts
        fallbackCopyTextToClipboard(linkInput.value, copyButton, successMessage);
    }
}

function fallbackCopyTextToClipboard(text, button, successMessage) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(button, successMessage);
        } else {
            showCopyError(button);
        }
    } catch (err) {
        showCopyError(button);
    }
    
    document.body.removeChild(textArea);
}

function showCopySuccess(button, successMessage) {
    // Update button
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-success');
    
    // Show success message
    successMessage.classList.remove('d-none');
    
    // Reset after 3 seconds
    setTimeout(function() {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
        successMessage.classList.add('d-none');
    }, 3000);
}

function showCopyError(button) {
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Failed';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-danger');
    
    setTimeout(function() {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-danger');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>

=======
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.getElementById('copy-link-btn');
    const linkInput = document.getElementById('mobile-login-link');
    
    if (copyBtn && linkInput) {
        copyBtn.addEventListener('click', async function() {
            try {
                // Try modern clipboard API first
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(linkInput.value);
                } else {
                    // Fallback for older browsers or non-HTTPS
                    linkInput.select();
                    linkInput.setSelectionRange(0, 99999); // For mobile devices
                    document.execCommand('copy');
                }
                
                // Show success feedback
                const originalText = copyBtn.textContent;
                copyBtn.textContent = 'Copied!';
                copyBtn.classList.add('btn-success');
                copyBtn.classList.remove('btn-outline-secondary');
                
                // Reset after 1.5 seconds
                setTimeout(() => {
                    copyBtn.textContent = originalText;
                    copyBtn.classList.remove('btn-success');
                    copyBtn.classList.add('btn-outline-secondary');
                }, 1500);
                
            } catch (err) {
                console.error('Failed to copy text: ', err);
                // Show error feedback
                copyBtn.textContent = 'Error!';
                copyBtn.classList.add('btn-danger');
                copyBtn.classList.remove('btn-outline-secondary');
                
                setTimeout(() => {
                    copyBtn.textContent = 'Copy';
                    copyBtn.classList.remove('btn-danger');
                    copyBtn.classList.add('btn-outline-secondary');
                }, 1500);
            }
        });
    }
});
</script>
>>>>>>> bf4f09e2d0fd51ad4360c6e9912471a0fe5dc319
@endsection