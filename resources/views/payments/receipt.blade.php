{{-- filepath: c:\Users\Pa\Herd\workflow-management\resources\views\payments\receipt.blade.php --}}
@extends('layouts.auth')

@section('content')
<div class="container-fluid mt-3">
    <div class="receipt-container">
        <div class="receipt" id="payment-receipt">
            <!-- Receipt Header -->
            <div class="receipt-header">
                <div class="company-info">
                    <h2 class="company-name">Your Company Name</h2>
                    <p class="company-details">
                        123 Business Street<br>
                        City, Province 1234<br>
                        Tel: (012) 345-6789
                    </p>
                </div>
                <div class="receipt-type">
                    <h3>PAYMENT RECEIPT</h3>
                    <p class="receipt-number">#{{ $payment->receipt_number }}</p>
                </div>
            </div>

            <!-- Receipt Details -->
            <div class="receipt-body">
                <div class="receipt-info-grid">
                    <div class="info-section">
                        <h4>Payment Details</h4>
                        <div class="info-row">
                            <span class="label">Receipt Number:</span>
                            <span class="value">{{ $payment->receipt_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Payment Date:</span>
                            <span class="value">{{ $payment->payment_date->format('M d, Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Payment Method:</span>
                            <span class="value">{{ ucfirst($payment->payment_method) }}</span>
                        </div>
                        @if($payment->reference_number)
                            <div class="info-row">
                                <span class="label">Reference Number:</span>
                                <span class="value">{{ $payment->reference_number }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="info-section">
                        <h4>Customer Details</h4>
                        <div class="info-row">
                            <span class="label">Name:</span>
                            <span class="value">{{ $payment->client->name }} {{ $payment->client->surname }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Payment Reference:</span>
                            <span class="value">{{ $payment->client->payment_reference }}</span>
                        </div>
                        @if($payment->client->email)
                            <div class="info-row">
                                <span class="label">Email:</span>
                                <span class="value">{{ $payment->client->email }}</span>
                            </div>
                        @endif
                        @if($payment->client->telephone)
                            <div class="info-row">
                                <span class="label">Phone:</span>
                                <span class="value">{{ $payment->client->telephone }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary">
                    @if($payment->invoice_jobcard_number)
                        <div class="summary-row">
                            <span class="label">Payment For:</span>
                            <span class="value">{{ $payment->invoice_jobcard_number }}</span>
                        </div>
                    @endif
                    
                    <div class="summary-row amount-row">
                        <span class="label">Amount Paid:</span>
                        <span class="value amount">R{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    
                    <div class="summary-row total-row">
                        <span class="label">Total Received:</span>
                        <span class="value total">R{{ number_format($payment->amount, 2) }}</span>
                    </div>
                </div>

                @if($payment->notes)
                    <div class="notes-section">
                        <h4>Notes</h4>
                        <p>{{ $payment->notes }}</p>
                    </div>
                @endif

                <!-- Footer -->
                <div class="receipt-footer">
                    <p class="thank-you">Thank you for your payment!</p>
                    <p class="processed-time">
                        Receipt generated on {{ $payment->created_at->format('M d, Y \a\t H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="receipt-actions">
            <button class="btn btn-secondary" onclick="goBackToClient()">
                👤 Back to Customer
            </button>
            <button class="btn btn-primary" onclick="printReceipt()">
                🖨️ Print Receipt
            </button>
            <button class="btn btn-info" onclick="emailReceipt()">
                📧 Email Receipt
            </button>
            <button class="btn btn-success" onclick="downloadPDF()">
                📄 Download PDF
            </button>
        </div>
    </div>
</div>

<script>
function goBackToClient() {
    window.location.href = '/client/{{ $payment->client_id }}';
}

function printReceipt() {
    window.print();
}

function emailReceipt() {
    alert('Email receipt functionality - to be implemented');
}

function downloadPDF() {
    alert('PDF download functionality - to be implemented');
}
</script>

<style>
.receipt-container {
    max-width: 800px;
    margin: 0 auto;
}

.receipt {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 40px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #dee2e6;
}

.company-name {
    margin: 0 0 10px 0;
    color: #495057;
    font-size: 1.5em;
    font-weight: 700;
}

.company-details {
    margin: 0;
    color: #6c757d;
    line-height: 1.4;
}

.receipt-type {
    text-align: right;
}

.receipt-type h3 {
    margin: 0 0 5px 0;
    color: #28a745;
    font-size: 1.3em;
    font-weight: 700;
}

.receipt-number {
    margin: 0;
    color: #6c757d;
    font-family: 'Courier New', monospace;
    font-weight: 600;
}

.receipt-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.info-section h4 {
    margin: 0 0 15px 0;
    color: #495057;
    font-size: 1.1em;
    font-weight: 600;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 5px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
}

.info-row .label {
    color: #6c757d;
    font-weight: 500;
}

.info-row .value {
    color: #495057;
    font-weight: 600;
}

.payment-summary {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 20px;
    margin-bottom: 30px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 5px 0;
}

.summary-row.amount-row {
    font-size: 1.1em;
}

.summary-row.total-row {
    border-top: 2px solid #28a745;
    margin-top: 15px;
    padding-top: 15px;
    font-size: 1.2em;
    font-weight: 700;
}

.summary-row .amount {
    color: #28a745;
    font-weight: 700;
}

.summary-row .total {
    color: #28a745;
    font-weight: 700;
    font-size: 1.3em;
}

.notes-section {
    margin-bottom: 30px;
    padding: 15px;
    background: #fff3cd;
    border-radius: 6px;
    border: 1px solid #ffeaa7;
}

.notes-section h4 {
    margin: 0 0 10px 0;
    color: #856404;
}

.notes-section p {
    margin: 0;
    color: #856404;
    font-style: italic;
}

.receipt-footer {
    text-align: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

.thank-you {
    margin: 0 0 10px 0;
    font-size: 1.1em;
    font-weight: 600;
    color: #28a745;
}

.processed-time {
    margin: 0;
    font-size: 0.8em;
    color: #6c757d;
}

.receipt-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
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

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Print Styles */
@media print {
    .receipt-actions {
        display: none;
    }
    
    .receipt {
        box-shadow: none;
        border: none;
        margin: 0;
        padding: 20px;
    }
    
    body {
        background: white;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .receipt {
        padding: 20px;
    }
    
    .receipt-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .receipt-info-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .receipt-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection