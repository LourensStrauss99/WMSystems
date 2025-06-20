{{-- filepath: c:\Users\Pa\Herd\workflow-management\resources\views\payments\create.blade.php --}}
@extends('layouts.auth')

@section('content')
<div class="container-fluid mt-3">
    <!-- Header -->
    <div class="payment-header mb-4">
        <div class="header-left">
            <a href="/client/{{ $client->id }}" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
                </svg>
                Back to Customer
            </a>
            <h2 class="payment-title">Process Payment</h2>
        </div>
        <div class="client-info">
            <span class="client-name">{{ $client->name }} {{ $client->surname }}</span>
            <span class="payment-ref">Ref: {{ $client->payment_reference }}</span>
        </div>
    </div>

    <div class="payment-container">
        <!-- Payment Form -->
        <div class="payment-card main-form">
            <div class="card-header">
                <h5>💳 Payment Details</h5>
                <span class="secure-badge">🔒 Secure</span>
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

                <form action="{{ route('payments.store') }}" method="POST" id="payment-form">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    
                    <!-- Invoice/Jobcard Selection -->
                    <div class="form-section">
                        <h6 class="section-title">📋 What are you paying for?</h6>
                        
                        <div class="payment-type-selector">
                            <label class="payment-type-option">
                                <input type="radio" name="payment_type" value="invoice" checked>
                                <div class="option-content">
                                    <span class="option-icon">🧾</span>
                                    <span class="option-text">Specific Invoice</span>
                                </div>
                            </label>
                            
                            <label class="payment-type-option">
                                <input type="radio" name="payment_type" value="jobcard">
                                <div class="option-content">
                                    <span class="option-icon">🔧</span>
                                    <span class="option-text">Jobcard</span>
                                </div>
                            </label>
                            
                            <label class="payment-type-option">
                                <input type="radio" name="payment_type" value="general">
                                <div class="option-content">
                                    <span class="option-icon">💰</span>
                                    <span class="option-text">General Payment</span>
                                </div>
                            </label>
                        </div>

                        <!-- Invoice/Jobcard Number Input -->
                        <div id="number-input-section" class="form-group">
                            <label for="invoice_jobcard_number" class="form-label">Invoice/Jobcard Number</label>
                            <div class="number-input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="invoice_jobcard_number" 
                                       name="invoice_jobcard_number"
                                       placeholder="Enter invoice or jobcard number">
                                <button type="button" class="lookup-btn" onclick="lookupNumber()">🔍 Lookup</button>
                            </div>
                            <div id="lookup-result" class="lookup-result"></div>
                        </div>
                    </div>

                    <!-- Payment Amount -->
                    <div class="form-section">
                        <h6 class="section-title">💵 Payment Amount</h6>
                        
                        <div class="form-group">
                            <label for="amount" class="form-label required">Amount (R)</label>
                            <div class="amount-input-group">
                                <span class="currency-symbol">R</span>
                                <input type="number" 
                                       class="form-control amount-input" 
                                       id="amount" 
                                       name="amount" 
                                       step="0.01" 
                                       min="0.01" 
                                       required
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h6 class="section-title">💳 Payment Method</h6>
                        
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <div class="method-content">
                                    <span class="method-icon">💵</span>
                                    <span class="method-name">Cash</span>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="card">
                                <div class="method-content">
                                    <span class="method-icon">💳</span>
                                    <span class="method-name">Card</span>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="eft">
                                <div class="method-content">
                                    <span class="method-icon">🏦</span>
                                    <span class="method-name">EFT</span>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="cheque">
                                <div class="method-content">
                                    <span class="method-icon">📝</span>
                                    <span class="method-name">Cheque</span>
                                </div>
                            </label>
                            
                            <label class="payment-method disabled">
                                <input type="radio" name="payment_method" value="payfast" disabled>
                                <div class="method-content">
                                    <span class="method-icon">📱</span>
                                    <span class="method-name">PayFast</span>
                                    <span class="coming-soon">Soon</span>
                                </div>
                            </label>
                        </div>

                        <!-- Reference Number (for EFT/Card) -->
                        <div id="reference-section" class="form-group" style="display: none;">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="reference_number" 
                                   name="reference_number"
                                   placeholder="Transaction reference number">
                        </div>
                    </div>

                    <!-- Payment Date -->
                    <div class="form-section">
                        <h6 class="section-title">📅 Payment Date</h6>
                        
                        <div class="form-group">
                            <label for="payment_date" class="form-label required">Date</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="payment_date" 
                                   name="payment_date" 
                                   value="{{ date('Y-m-d') }}" 
                                   required>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-section">
                        <h6 class="section-title">📝 Additional Notes</h6>
                        
                        <div class="form-group">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Any additional notes about this payment..."></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="cancelPayment()">
                            ❌ Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="process-btn">
                            💳 Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Selection Sidebar -->
        <div class="payment-card sidebar">
            <!-- Unpaid Invoices -->
            @if(count($unpaidInvoices) > 0)
                <div class="quick-section">
                    <h6 class="quick-title">🧾 Unpaid Invoices</h6>
                    <div class="quick-items">
                        @foreach($unpaidInvoices as $invoice)
                            <div class="quick-item" onclick="selectInvoice('{{ $invoice->invoice_number }}', {{ $invoice->amount }})">
                                <div class="item-number">#{{ $invoice->invoice_number }}</div>
                                <div class="item-amount">R{{ number_format($invoice->amount, 2) }}</div>
                                <div class="item-date">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Completed Jobcards -->
            @if(count($completedJobcards) > 0)
                <div class="quick-section">
                    <h6 class="quick-title">🔧 Completed Jobs</h6>
                    <div class="quick-items">
                        @foreach($completedJobcards as $jobcard)
                            <div class="quick-item" onclick="selectJobcard('{{ $jobcard->jobcard_number }}', {{ $jobcard->amount ?? 0 }})">
                                <div class="item-number">#{{ $jobcard->jobcard_number }}</div>
                                <div class="item-amount">R{{ number_format($jobcard->amount ?? 0, 2) }}</div>
                                <div class="item-date">{{ \Carbon\Carbon::parse($jobcard->job_date)->format('M d, Y') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Payment Portal Placeholder -->
            <div class="quick-section">
                <h6 class="quick-title">🌐 Online Payments</h6>
                <div class="portal-placeholder">
                    <p class="portal-text">Online payment gateway coming soon!</p>
                    <div class="portal-features">
                        <div class="feature">✓ Secure card payments</div>
                        <div class="feature">✓ Instant confirmation</div>
                        <div class="feature">✓ Mobile-friendly</div>
                    </div>
                    <button class="portal-setup-btn" disabled>
                        ⚙️ Setup Portal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Payment type selection
document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const numberSection = document.getElementById('number-input-section');
        const numberInput = document.getElementById('invoice_jobcard_number');
        
        if (this.value === 'general') {
            numberSection.style.display = 'none';
            numberInput.required = false;
            numberInput.value = '';
        } else {
            numberSection.style.display = 'block';
            numberInput.required = true;
            numberInput.placeholder = this.value === 'invoice' ? 
                'Enter invoice number' : 'Enter jobcard number';
        }
        
        clearLookupResult();
    });
});

// Payment method selection
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const referenceSection = document.getElementById('reference-section');
        const referenceInput = document.getElementById('reference_number');
        
        if (this.value === 'eft' || this.value === 'card') {
            referenceSection.style.display = 'block';
            referenceInput.required = true;
        } else {
            referenceSection.style.display = 'none';
            referenceInput.required = false;
            referenceInput.value = '';
        }
    });
});

// Lookup invoice/jobcard number
function lookupNumber() {
    const number = document.getElementById('invoice_jobcard_number').value.trim();
    const resultDiv = document.getElementById('lookup-result');
    
    if (!number) {
        showLookupResult('Please enter a number to lookup', 'error');
        return;
    }
    
    resultDiv.innerHTML = '<div class="lookup-loading">🔍 Looking up...</div>';
    
    fetch('/payments/lookup-invoice', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            invoice_jobcard_number: number,
            client_id: {{ $client->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.found) {
            showLookupResult(
                `${data.type.charAt(0).toUpperCase() + data.type.slice(1)} found! Amount: R${parseFloat(data.amount).toFixed(2)} (${data.status})`,
                'success'
            );
            document.getElementById('amount').value = parseFloat(data.amount).toFixed(2);
        } else {
            showLookupResult('No invoice or jobcard found with this number', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showLookupResult('Error looking up number', 'error');
    });
}

function showLookupResult(message, type) {
    const resultDiv = document.getElementById('lookup-result');
    resultDiv.innerHTML = `<div class="lookup-${type}">${message}</div>`;
}

function clearLookupResult() {
    document.getElementById('lookup-result').innerHTML = '';
}

// Quick selection functions
function selectInvoice(invoiceNumber, amount) {
    document.querySelector('input[name="payment_type"][value="invoice"]').checked = true;
    document.getElementById('invoice_jobcard_number').value = invoiceNumber;
    document.getElementById('amount').value = amount.toFixed(2);
    document.getElementById('number-input-section').style.display = 'block';
    lookupNumber();
}

function selectJobcard(jobcardNumber, amount) {
    document.querySelector('input[name="payment_type"][value="jobcard"]').checked = true;
    document.getElementById('invoice_jobcard_number').value = jobcardNumber;
    document.getElementById('amount').value = amount.toFixed(2);
    document.getElementById('number-input-section').style.display = 'block';
    lookupNumber();
}

function cancelPayment() {
    if (confirm('Are you sure you want to cancel this payment?')) {
        window.location.href = '/client/{{ $client->id }}';
    }
}

// Form submission
document.getElementById('payment-form').addEventListener('submit', function(e) {
    const processBtn = document.getElementById('process-btn');
    processBtn.disabled = true;
    processBtn.innerHTML = '⏳ Processing...';
});
</script>

<style>
/* Payment Header */
.payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.payment-title {
    margin: 0;
    color: #155724;
    font-size: 1.8em;
    font-weight: 600;
}

.client-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}

.client-name {
    font-size: 1.1em;
    font-weight: 600;
    color: #155724;
}

.payment-ref {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    background: #fff;
    color: #155724;
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid #155724;
}

/* Payment Container */
.payment-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

/* Payment Cards */
.payment-card {
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

.secure-badge {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.7em;
    font-weight: 600;
}

.card-content {
    padding: 20px;
}

/* Form Sections */
.form-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.section-title {
    margin: 0 0 15px 0;
    font-size: 1em;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 8px;
}

/* Payment Type Selector */
.payment-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.payment-type-option {
    cursor: pointer;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 0;
    transition: all 0.2s ease;
}

.payment-type-option:hover {
    border-color: #007bff;
}

.payment-type-option input[type="radio"] {
    display: none;
}

.payment-type-option input[type="radio"]:checked + .option-content {
    background: #007bff;
    color: white;
}

.option-content {
    padding: 16px 12px;
    text-align: center;
    background: #fff;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.option-icon {
    display: block;
    font-size: 1.5em;
    margin-bottom: 8px;
}

.option-text {
    font-size: 0.9em;
    font-weight: 600;
}

/* Payment Methods */
.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.payment-method {
    cursor: pointer;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 0;
    transition: all 0.2s ease;
}

.payment-method:hover {
    border-color: #28a745;
}

.payment-method.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.payment-method input[type="radio"] {
    display: none;
}

.payment-method input[type="radio"]:checked + .method-content {
    background: #28a745;
    color: white;
}

.method-content {
    padding: 16px 8px;
    text-align: center;
    background: #fff;
    border-radius: 6px;
    transition: all 0.2s ease;
    position: relative;
}

.method-icon {
    display: block;
    font-size: 1.2em;
    margin-bottom: 6px;
}

.method-name {
    font-size: 0.8em;
    font-weight: 600;
}

.coming-soon {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #ffc107;
    color: #212529;
    font-size: 0.6em;
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: 600;
}

/* Form Inputs */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
    font-size: 0.9em;
    display: block;
}

.form-label.required::after {
    content: ' *';
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 0.95em;
    transition: all 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* Special Input Groups */
.number-input-group, .amount-input-group {
    display: flex;
    gap: 8px;
    align-items: center;
}

.number-input-group .form-control {
    flex: 1;
}

.lookup-btn {
    background: #17a2b8;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.8em;
    white-space: nowrap;
}

.lookup-btn:hover {
    background: #138496;
}

.amount-input-group {
    position: relative;
}

.currency-symbol {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-weight: 600;
    color: #495057;
    z-index: 1;
}

.amount-input {
    padding-left: 32px !important;
    font-weight: 600;
    font-size: 1.1em;
}

/* Lookup Results */
.lookup-result {
    margin-top: 8px;
}

.lookup-loading, .lookup-success, .lookup-error {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 600;
}

.lookup-loading {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.lookup-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.lookup-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
    flex: 1;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Sidebar */
.sidebar {
    max-height: 80vh;
    overflow-y: auto;
}

.quick-section {
    margin-bottom: 20px;
}

.quick-title {
    margin: 0 0 12px 0;
    font-size: 0.9em;
    font-weight: 600;
    color: #495057;
    padding: 12px 16px;
    background: #f8f9fa;
    border-radius: 6px 6px 0 0;
    border-bottom: 1px solid #dee2e6;
}

.quick-items {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 0 16px 16px 16px;
}

.quick-item {
    padding: 12px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.quick-item:hover {
    background: #e9ecef;
    border-color: #007bff;
    transform: translateY(-1px);
}

.item-number {
    font-weight: 600;
    color: #007bff;
    font-size: 0.9em;
}

.item-amount {
    font-weight: 700;
    color: #28a745;
    font-size: 1em;
}

.item-date {
    font-size: 0.8em;
    color: #6c757d;
}

/* Portal Placeholder */
.portal-placeholder {
    padding: 16px;
    text-align: center;
}

.portal-text {
    font-size: 0.9em;
    color: #6c757d;
    margin-bottom: 12px;
}

.portal-features {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 12px;
}

.feature {
    font-size: 0.8em;
    color: #28a745;
    text-align: left;
}

.portal-setup-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 0.8em;
    cursor: not-allowed;
    opacity: 0.6;
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

/* Responsive Design */
@media (max-width: 1024px) {
    .payment-container {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        order: -1;
        max-height: none;
    }
}

@media (max-width: 768px) {
    .payment-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .payment-type-selector {
        grid-template-columns: 1fr;
    }
    
    .payment-methods {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>
@endsection