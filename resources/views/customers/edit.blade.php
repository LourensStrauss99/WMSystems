{{-- filepath: c:\Users\Pa\Herd\workflow-management\resources\views\customers\edit.blade.php --}}
@extends('layouts.auth')

@section('content')
<div class="container-fluid mt-3">
    <!-- Header -->
    <div class="edit-header mb-4">
        <div class="header-left">
            <a href="/client/{{ $customer->id }}" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
                </svg>
                Back to Customer
            </a>
            <h2 class="edit-title">Edit Customer Details</h2>
        </div>
        <div class="header-info">
            <span class="customer-id">Customer #{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="edit-container">
        <div class="edit-card">
            <div class="card-header">
                <h5>👤 Customer Information</h5>
                <span class="required-note">* Required fields</span>
            </div>
            
            <div class="card-content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/client/{{ $customer->id }}" method="POST" id="customer-edit-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-grid">
                        <!-- Name Fields -->
                        <div class="form-group">
                            <label for="name" class="form-label required">First Name *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $customer->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="surname" class="form-label">Last Name</label>
                            <input type="text" 
                                   class="form-control @error('surname') is-invalid @enderror" 
                                   id="surname" 
                                   name="surname" 
                                   value="{{ old('surname', $customer->surname) }}">
                            @error('surname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contact Fields -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $customer->email) }}"
                                   placeholder="customer@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telephone" class="form-label">Phone Number</label>
                            <input type="tel" 
                                   class="form-control @error('telephone') is-invalid @enderror" 
                                   id="telephone" 
                                   name="telephone" 
                                   value="{{ old('telephone', $customer->telephone) }}"
                                   placeholder="e.g. 0123456789">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address Field -->
                        <div class="form-group full-width">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3"
                                      placeholder="Street address, city, postal code">{{ old('address', $customer->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes Field -->
                        <div class="form-group full-width">
                            <label for="notes" class="form-label">Customer Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="Add any relevant notes about this customer...">{{ old('notes', $customer->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Notes about customer preferences, special requirements, payment terms, etc.
                            </div>
                        </div>

                        <!-- Payment Reference Field -->
                        <div class="form-group">
                            <label for="payment_reference" class="form-label">Payment Reference</label>
                            <div class="reference-input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="payment_reference" 
                                       name="payment_reference" 
                                       value="{{ $customer->payment_reference }}" 
                                       readonly>
                                <button type="button" class="regenerate-btn" onclick="regenerateReference()">
                                    🔄 New
                                </button>
                            </div>
                            <div class="form-text">
                                Unique reference for payment tracking. Auto-generated from surname + 5 digits.
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                                ❌ Cancel
                            </button>
                            <button type="button" class="btn btn-warning" onclick="resetForm()">
                                🔄 Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                💾 Save Changes
                            </button>
                        </div>
                        Last updated: {{ $customer->updated_at ? $customer->updated_at->format('M d, Y \a\t H:i') : 'Never' }}
                        <div class="last-updated">
                            <small class="text-muted">
                                
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customer Preview -->
        <div class="preview-card">
            <div class="card-header">
                <h5>👁️ Live Preview</h5>
            </div>
            <div class="card-content">
                <div class="preview-content">
                    <div class="preview-item">
                        <span class="preview-label">Full Name:</span>
                        <span class="preview-value" id="preview-name">{{ $customer->name }} {{ $customer->surname }}</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-label">Email:</span>
                        <span class="preview-value" id="preview-email">{{ $customer->email ?: 'Not provided' }}</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-label">Phone:</span>
                        <span class="preview-value" id="preview-phone">{{ $customer->telephone ?: 'Not provided' }}</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-label">Address:</span>
                        <span class="preview-value" id="preview-address">{{ $customer->address ?: 'Not provided' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add this after the summary-stats div in your account-summary card: --}}
    {{-- <div class="payment-portal">
        <h6>💳 Payment Portal (Coming Soon)</h6>
        <div class="portal-placeholder">
            <div class="portal-info">
                <p><strong>Customer Reference:</strong> 
                    <code class="ref-code">{{ $customer->payment_reference ?: 'Generating...' }}</code>
                    @if($customer->payment_reference)
                        <button class="copy-mini-btn" onclick="copyReference('{{ $customer->payment_reference }}')" title="Copy">📋</button>
                    @endif
                </p>
            </div>
            <div class="portal-actions">
                <button class="portal-btn disabled" disabled>🏦 PayFast</button>
                <button class="portal-btn disabled" disabled>💳 Stripe</button>
                <button class="portal-btn disabled" disabled>📱 PayPal</button>
            </div>
            <div class="setup-notice">
                <small>💡 Configure payment gateway in system settings</small>
            </div>
        </div>
    </div>--}}





    
</div>

<script>
// Live preview updates
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const surnameInput = document.getElementById('surname');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('telephone');
    const addressInput = document.getElementById('address');

    function updatePreview() {
        const fullName = `${nameInput.value || ''} ${surnameInput.value || ''}`.trim();
        document.getElementById('preview-name').textContent = fullName || 'Not provided';
        document.getElementById('preview-email').textContent = emailInput.value || 'Not provided';
        document.getElementById('preview-phone').textContent = phoneInput.value || 'Not provided';
        document.getElementById('preview-address').textContent = addressInput.value || 'Not provided';
    }

    // Add event listeners for live preview
    [nameInput, surnameInput, emailInput, phoneInput, addressInput].forEach(input => {
        if (input) {
            input.addEventListener('input', updatePreview);
        }
    });

    // Form validation
    const form = document.getElementById('customer-edit-form');
    form.addEventListener('submit', function(e) {
        const saveBtn = document.getElementById('save-btn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '⏳ Saving...';
    });
});

function cancelEdit() {
    if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
        window.location.href = '/client/{{ $customer->id }}';
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset all fields to their original values?')) {
        document.getElementById('customer-edit-form').reset();
        // Update preview after reset
        setTimeout(() => {
            document.getElementById('preview-name').textContent = '{{ $customer->name }} {{ $customer->surname }}';
            document.getElementById('preview-email').textContent = '{{ $customer->email ?: "Not provided" }}';
            document.getElementById('preview-phone').textContent = '{{ $customer->telephone ?: "Not provided" }}';
            document.getElementById('preview-address').textContent = '{{ $customer->address ?: "Not provided" }}';
        }, 100);
    }
}

// Add to your existing <script> section
function regenerateReference() {
    if (confirm('Generate a new payment reference? This will change how customers identify payments.')) {
        fetch('/client/{{ $customer->id }}/regenerate-reference', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('payment_reference').value = data.reference;
                alert('New payment reference generated: ' + data.reference);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating new reference');
        });
    }
}

// Auto-save draft functionality (optional)
let autoSaveTimeout;
function autoSaveDraft() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        // You can implement auto-save to localStorage here if needed
        console.log('Auto-saving draft...');
    }, 2000);
}

// Add auto-save to all inputs
document.querySelectorAll('input, textarea').forEach(input => {
    input.addEventListener('input', autoSaveDraft);
});
</script>

<style>
/* Edit Header */
.edit-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.back-btn {
    background: #6c757d;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9em;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.back-btn:hover {
    background: #5a6268;
    text-decoration: none;
    color: white;
    transform: translateY(-1px);
}

.edit-title {
    margin: 0;
    color: #1976d2;
    font-size: 1.8em;
    font-weight: 600;
}

.customer-id {
    background: #fff;
    color: #1976d2;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9em;
    font-weight: 600;
    border: 2px solid #1976d2;
}

/* Edit Container */
.edit-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    align-items: start;
}

/* Cards */
.edit-card, .preview-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.08);
    overflow: hidden;
}

.card-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    font-size: 1.1em;
    font-weight: 600;
    color: #495057;
}

.required-note {
    font-size: 0.8em;
    color: #dc3545;
    font-style: italic;
}

.card-content {
    padding: 20px;
}

/* Form Styling */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
    font-size: 0.9em;
}

.form-label.required::after {
    content: ' *';
    color: #dc3545;
}

.form-control {
    padding: 10px 12px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 0.95em;
    transition: all 0.2s ease;
    background: #fff;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.8em;
    margin-top: 4px;
}

.form-text {
    font-size: 0.8em;
    color: #6c757d;
    margin-top: 4px;
}

/* Alert */
.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert h6 {
    margin-bottom: 8px;
    font-weight: 600;
}

.alert ul {
    margin-left: 20px;
}

/* Form Actions */
.form-actions {
    border-top: 1px solid #dee2e6;
    padding-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.action-buttons {
    display: flex;
    gap: 12px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.last-updated {
    text-align: right;
}

/* Preview Card */
.preview-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.preview-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.preview-label {
    font-size: 0.8em;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
}

.preview-value {
    font-size: 0.95em;
    color: #495057;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .edit-container {
        grid-template-columns: 1fr;
    }
    
    .preview-card {
        order: -1;
    }
}

@media (max-width: 768px) {
    .edit-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .header-left {
        flex-direction: column;
        gap: 10px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .action-buttons {
        flex-direction: column;
        width: 100%;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Loading States */
.btn:disabled {
    position: relative;
}

.btn:disabled::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: button-loading-spinner 1s ease infinite;
}

@keyframes button-loading-spinner {
    from {
        transform: rotate(0turn);
    }
    to {
        transform: rotate(1turn);
    }
}

/* Add to your existing <style> section */
.reference-input-group {
    display: flex;
    gap: 8px;
    align-items: center;
}

.reference-input-group .form-control {
    flex: 1;
    background-color: #f8f9fa;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #007bff;
}

.regenerate-btn {
    background: #17a2b8;
    color: white;
    border: none;
    padding: 10px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.8em;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.regenerate-btn:hover {
    background: #138496;
    transform: translateY(-1px);
}

.payment-portal {
    margin-top: 30px;
    padding: 20px;
    background: #f1f3f5;
    border-radius: 12px;
    border: 1px solid #dee2e6;
}

.portal-placeholder {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.portal-info {
    background: #fff;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.ref-code {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #007bff;
    background: #e9ecef;
    padding: 4px 8px;
    border-radius: 4px;
}

.copy-mini-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 6px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.75em;
    transition: all 0.2s ease;
    margin-left: 8px;
}

.copy-mini-btn:hover {
    background: #0056b3;
}

.portal-actions {
    display: flex;
    gap: 12px;
}

.portal-btn {
    flex: 1;
    padding: 10px 0;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.portal-btn.disabled {
    background: #6c757d;
    color: white;
    cursor: not-allowed;
}

.setup-notice {
    text-align: center;
    font-size: 0.8em;
    color: #6c757d;
}
</style>
@endsection