{{-- filepath: resources/views/invoice_view.blade.php --}}
@extends('layouts.inv')

@section('content')
<div class="invoice-container">
    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="company-logo">
            @if(!empty($company->company_logo))
                <img src="{{ asset('storage/' . $company->company_logo) }}" alt="{{ $company->company_name }}" class="logo">
            @else
                <img src="{{ asset('silogo.jpg') }}" alt="Company Logo" class="logo">
            @endif
        </div>
        <div class="company-details">
            <h1 class="company-name">{{ $company->company_name }}</h1>
            <div class="company-address">
                {{ $company->address }}<br>
                {{ $company->city }}, {{ $company->province }} {{ $company->postal_code }}<br>
                {{ $company->country }}
            </div>
            <div class="company-contact">
                <i class="fas fa-phone"></i> {{ $company->company_telephone }} | 
                <i class="fas fa-envelope"></i> {{ $company->company_email }}
                @if($company->company_website)
                    <br><i class="fas fa-globe"></i> {{ $company->company_website }}
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Details Header -->
    <div class="invoice-details-header">
        <div class="client-details">
            <h3><i class="fas fa-user"></i> Invoice To:</h3>
            <div class="client-info">
                <strong>{{ $jobcard->client->name }}</strong><br>
                @if($jobcard->client->address)
                    {{ $jobcard->client->address }}<br>
                @endif
                @if($jobcard->client->email)
                    <i class="fas fa-envelope"></i> {{ $jobcard->client->email }}<br>
                @endif
                @if($jobcard->client->telephone)
                    <i class="fas fa-phone"></i> {{ $jobcard->client->telephone }}
                @endif
            </div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-number">
                <h3>Invoice #: {{ $jobcard->jobcard_number }}</h3>
            </div>
            <div class="invoice-date">
                <strong>Date:</strong> {{ $jobcard->job_date ?? $jobcard->created_at->format('Y-m-d') }}
            </div>
            <div class="due-date">
                <strong>Due Date:</strong> {{ $jobcard->created_at->addDays(30)->format('Y-m-d') }}
            </div>
        </div>
    </div>

    <!-- Work Done Section -->
    @if(!empty($jobcard->work_done))
        <div class="work-done-section">
            <h3><i class="fas fa-wrench"></i> Work Description</h3>
            <div class="work-description">
                {{ $jobcard->work_done }}
            </div>
        </div>
    @endif

    <!-- Invoice Items Table -->
    <div class="invoice-table-section">
        <h3><i class="fas fa-list"></i> Invoice Details</h3>
        
        <table class="invoice-table">
            <thead>
                <tr>
                    <th class="item-desc">Item / Description</th>
                    <th class="qty">Qty</th>
                    <th class="unit-price">Unit Price</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $inventoryTotal = 0; @endphp
                
                <!-- Inventory Items -->
                @foreach($jobcard->inventory as $item)
                    @php
                        $lineTotal = $item->pivot->quantity * $item->selling_price;
                        $inventoryTotal += $lineTotal;
                    @endphp
                    <tr class="item-row">
                        <td class="item-desc">
                            <strong>{{ $item->name }}</strong>
                            @if($item->short_description)
                                <br><small class="text-muted">{{ $item->short_description }}</small>
                            @endif
                        </td>
                        <td class="qty">{{ $item->pivot->quantity }}</td>
                        <td class="unit-price">R {{ number_format($item->selling_price, 2) }}</td>
                        <td class="total">R {{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
                
                @if($inventoryTotal > 0)
                    <tr class="subtotal-row">
                        <td colspan="3" class="subtotal-label">Inventory Subtotal</td>
                        <td class="subtotal-amount">R {{ number_format($inventoryTotal, 2) }}</td>
                    </tr>
                @endif

                <!-- Labour Section -->
                @php
                    $labourHours = $jobcard->employees->sum(fn($employee) => $employee->pivot->hours_worked ?? 0);
                    $labourTotal = $labourHours * $company->labour_rate;
                @endphp
                
                @if($labourHours > 0)
                    <tr class="section-header">
                        <td colspan="4" class="section-title">
                            <i class="fas fa-tools"></i> Labour Services
                        </td>
                    </tr>
                    <tr class="item-row">
                        <td class="item-desc">
                            <strong>Professional Labour</strong>
                            <br><small class="text-muted">{{ number_format($labourHours, 2) }} hours @ R{{ number_format($company->labour_rate, 2) }}/hour</small>
                        </td>
                        <td class="qty">{{ number_format($labourHours, 2) }}</td>
                        <td class="unit-price">R {{ number_format($company->labour_rate, 2) }}</td>
                        <td class="total">R {{ number_format($labourTotal, 2) }}</td>
                    </tr>
                @endif

                <!-- Totals -->
                @php
                    $subtotal = $inventoryTotal + $labourTotal;
                    $vat = $subtotal * ($company->vat_percent / 100);
                    $grandTotal = $subtotal + $vat;
                @endphp
                
                <tr class="totals-section">
                    <td colspan="4" class="totals-spacer"></td>
                </tr>
                <tr class="subtotal-row">
                    <td colspan="3" class="subtotal-label">Subtotal:</td>
                    <td class="subtotal-amount">R {{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr class="vat-row">
                    <td colspan="3" class="vat-label">VAT ({{ $company->vat_percent }}%):</td>
                    <td class="vat-amount">R {{ number_format($vat, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="total-label">Total Amount Due:</td>
                    <td class="total-amount">R {{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Information -->
    <div class="payment-info-section">
        <div class="banking-details">
            <h3><i class="fas fa-university"></i> Banking Details</h3>
            <div class="bank-info">
                <div class="bank-row"><strong>Bank:</strong> {{ $company->bank_name }}</div>
                <div class="bank-row"><strong>Account Holder:</strong> {{ $company->account_holder }}</div>
                <div class="bank-row"><strong>Account Number:</strong> {{ $company->account_number }}</div>
                <div class="bank-row"><strong>Branch Code:</strong> {{ $company->branch_code }}</div>
                @if($company->swift_code)
                    <div class="bank-row"><strong>SWIFT/BIC:</strong> {{ $company->swift_code }}</div>
                @endif
            </div>
        </div>
        
        <div class="payment-terms">
            <h3><i class="fas fa-handshake"></i> Payment Terms</h3>
            <div class="terms-content">
                {{ $company->invoice_terms ?? 'Payment due within 30 days' }}
            </div>
        </div>
    </div>

    <!-- Footer Notes -->
    @if($company->invoice_footer)
        <div class="invoice-footer">
            <div class="footer-content">
                {{ $company->invoice_footer }}
            </div>
        </div>
    @endif

    <!-- Action Buttons (No Print) -->
    <div class="invoice-actions no-print">
        <div class="action-buttons">
            <form method="POST" action="{{ route('invoice.email', $jobcard->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn email-btn">
                    <i class="fas fa-envelope"></i> Email Invoice
                </button>
            </form>
            
            <!-- NEW PDF BUTTON -->
            <a href="{{ route('invoice.pdf', $jobcard->id) }}" class="action-btn pdf-btn" target="_blank">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            
            <a href="{{ route('invoice.index') }}" class="action-btn back-btn">
                <i class="fas fa-arrow-left"></i> Back to Invoices
            </a>
        </div>
    </div>
</div>
@endsection

<style>
/* Modern Invoice Styling */
.invoice-container {
    max-width: 210mm;
    margin: 0 auto;
    background: white;
    padding: 20mm;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
}

/* Header Styling */
.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #007bff;
}

.company-logo .logo {
    max-width: 150px;
    max-height: 100px;
    object-fit: contain;
}

.company-details {
    text-align: right;
    flex-grow: 1;
    margin-left: 20px;
}

.company-name {
    font-size: 28px;
    font-weight: bold;
    color: #007bff;
    margin: 0 0 10px 0;
}

.company-address, .company-contact {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
}

/* Invoice Details Header */
.invoice-details-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.client-details h3, .invoice-meta h3 {
    color: #007bff;
    margin-bottom: 15px;
    font-size: 18px;
}

.client-info {
    font-size: 14px;
    line-height: 1.8;
}

.invoice-meta {
    text-align: right;
}

.invoice-number {
    font-size: 20px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 10px;
}

.invoice-date, .due-date {
    margin-bottom: 8px;
    font-size: 14px;
}

/* Work Done Section */
.work-done-section {
    margin-bottom: 30px;
    background: #e8f4fd;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.work-done-section h3 {
    color: #007bff;
    margin-bottom: 15px;
}

.work-description {
    font-size: 14px;
    line-height: 1.7;
    color: #444;
}

/* Invoice Table */
.invoice-table-section {
    margin-bottom: 30px;
}

.invoice-table-section h3 {
    color: #007bff;
    margin-bottom: 20px;
    font-size: 18px;
}

.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 14px;
}

.invoice-table thead tr {
    background: #007bff;
    color: white;
}

.invoice-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border: 1px solid #dee2e6;
}

.invoice-table th.qty, 
.invoice-table th.unit-price, 
.invoice-table th.total,
.invoice-table td.qty, 
.invoice-table td.unit-price, 
.invoice-table td.total {
    text-align: right;
    width: 12%;
}

.invoice-table th.item-desc,
.invoice-table td.item-desc {
    width: 64%;
}

.invoice-table td {
    padding: 12px;
    border: 1px solid #dee2e6;
    vertical-align: top;
}

.item-row:nth-child(even) {
    background: #f8f9fa;
}

.section-header td {
    background: #e9ecef !important;
    font-weight: bold;
    color: #495057;
    padding: 10px 12px;
}

.subtotal-row, .vat-row {
    background: #f8f9fa;
    font-weight: 600;
}

.total-row {
    background: #007bff;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.subtotal-label, .vat-label, .total-label {
    text-align: right;
    padding: 12px;
}

.subtotal-amount, .vat-amount, .total-amount {
    text-align: right;
    padding: 12px;
}

.totals-spacer {
    border: none;
    padding: 10px;
}

/* Payment Information */
.payment-info-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    gap: 30px;
}

.banking-details, .payment-terms {
    flex: 1;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.banking-details h3, .payment-terms h3 {
    color: #007bff;
    margin-bottom: 15px;
    font-size: 16px;
}

.bank-info, .terms-content {
    font-size: 14px;
    line-height: 1.6;
}

.bank-row {
    margin-bottom: 8px;
}

/* Footer */
.invoice-footer {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 30px;
    font-style: italic;
    color: #666;
}

/* Action Buttons */
.invoice-actions {
    text-align: center;
    padding: 20px;
    border-top: 2px solid #007bff;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.email-btn {
    background: #28a745;
    color: white;
}

.email-btn:hover {
    background: #218838;
    transform: translateY(-2px);
}

.pdf-btn {
    background: #dc3545 !important;
    color: white !important;
}

.pdf-btn:hover {
    background: #c82333 !important;
    transform: translateY(-2px) !important;
}

.print-btn {
    background: #007bff;
    color: white;
}

.print-btn:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

.back-btn {
    background: #6c757d;
    color: white;
}

.back-btn:hover {
    background: #545b62;
    transform: translateY(-2px);
}

/* Replace your print CSS with this BALANCED version for professional single-page printing */

@media print {
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-width: 0 !important;
        font-size: 9px !important; /* Better readable font size */
        line-height: 1.2 !important; /* Readable line height */
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .invoice-container {
        box-shadow: none !important;
        padding: 6mm !important; /* Reasonable padding */
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100vh !important;
        max-height: 100vh !important;
        overflow: hidden !important;
        page-break-inside: avoid !important;
        transform: scale(0.78) !important; /* Balanced scaling */
        transform-origin: top left !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    /* Professional header */
    .invoice-header {
        page-break-inside: avoid !important;
        margin-bottom: 8px !important;
        padding-bottom: 6px !important;
        border-bottom: 2px solid #007bff !important;
        display: flex !important;
        align-items: flex-start !important;
    }
    
    .company-name {
        font-size: 18px !important;
        margin-bottom: 3px !important;
        line-height: 1.1 !important;
        font-weight: bold !important;
    }
    
    .company-logo .logo {
        max-width: 70px !important;
        max-height: 50px !important;
    }
    
    .company-address, .company-contact {
        font-size: 8px !important;
        margin-bottom: 2px !important;
        line-height: 1.2 !important;
    }
    
    /* Professional invoice details header */
    .invoice-details-header {
        margin-bottom: 8px !important;
        padding: 8px !important;
        font-size: 8px !important;
        background: #f8f9fa !important;
        border-radius: 4px !important;
    }
    
    .invoice-details-header h3 {
        font-size: 11px !important;
        margin-bottom: 4px !important;
        font-weight: bold !important;
    }
    
    .invoice-number {
        font-size: 14px !important;
        margin-bottom: 3px !important;
        font-weight: bold !important;
    }
    
    .invoice-date, .due-date {
        margin-bottom: 2px !important;
        font-size: 8px !important;
    }
    
    /* Professional work done section */
    .work-done-section {
        margin-bottom: 8px !important;
        padding: 8px !important;
        background: #e8f4fd !important;
        border-radius: 4px !important;
        border-left: 3px solid #007bff !important;
    }
    
    .work-done-section h3 {
        font-size: 11px !important;
        margin-bottom: 4px !important;
        font-weight: bold !important;
    }
    
    .work-description {
        font-size: 8px !important;
        line-height: 1.3 !important;
        max-height: 30px !important;
        overflow: hidden !important;
    }
    
    /* Professional table */
    .invoice-table-section {
        margin-bottom: 8px !important;
    }
    
    .invoice-table-section h3 {
        font-size: 11px !important;
        margin-bottom: 6px !important;
        font-weight: bold !important;
    }
    
    .invoice-table {
        font-size: 8px !important;
        margin-bottom: 6px !important;
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
    }
    
    .invoice-table th {
        padding: 4px 3px !important;
        font-size: 8px !important;
        font-weight: 600 !important;
        border: 1px solid #333 !important;
        background: #007bff !important;
        color: white !important;
        line-height: 1.1 !important;
    }
    
    .invoice-table td {
        padding: 3px !important;
        border: 1px solid #ddd !important;
        vertical-align: top !important;
        font-size: 8px !important;
        line-height: 1.2 !important;
    }
    
    /* Balanced table column widths */
    .invoice-table th.item-desc,
    .invoice-table td.item-desc {
        width: 45% !important;
    }
    
    .invoice-table th.qty, 
    .invoice-table th.unit-price, 
    .invoice-table th.total,
    .invoice-table td.qty, 
    .invoice-table td.unit-price, 
    .invoice-table td.total {
        width: 18% !important;
        text-align: right !important;
    }
    
    .item-row:nth-child(even) {
        background: #f9f9f9 !important;
    }
    
    .section-header td {
        background: #e9ecef !important;
        font-weight: bold !important;
        padding: 4px 3px !important;
        font-size: 8px !important;
    }
    
    .subtotal-row, .vat-row {
        background: #f8f9fa !important;
        font-weight: 600 !important;
    }
    
    .total-row {
        background: #007bff !important;
        color: white !important;
        font-weight: bold !important;
        font-size: 9px !important;
    }
    
    .totals-spacer {
        border: none !important;
        padding: 3px !important;
        height: 6px !important;
    }
    
    /* Professional payment information */
    .payment-info-section {
        margin-bottom: 8px !important;
        gap: 10px !important;
        font-size: 8px !important;
        display: flex !important;
    }
    
    .banking-details, .payment-terms {
        padding: 8px !important;
        background: #f8f9fa !important;
        flex: 1 !important;
        border-radius: 4px !important;
    }
    
    .banking-details h3, .payment-terms h3 {
        font-size: 10px !important;
        margin-bottom: 4px !important;
        font-weight: bold !important;
    }
    
    .bank-info, .terms-content {
        font-size: 8px !important;
        line-height: 1.3 !important;
    }
    
    .bank-row {
        margin-bottom: 2px !important;
    }
    
    /* Professional footer - Keep it visible but compact */
    .invoice-footer {
        padding: 6px !important;
        margin-bottom: 6px !important;
        font-size: 7px !important;
        background: #f8f9fa !important;
        border-radius: 4px !important;
        text-align: center !important;
        font-style: italic !important;
    }
    
    /* Force single page */
    .invoice-container * {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    /* Reasonable spacing between sections */
    .invoice-header { margin-bottom: 8px !important; }
    .invoice_details-header { margin-bottom: 8px !important; }
    .work-done-section { margin-bottom: 8px !important; }
    .invoice-table-section { margin-bottom: 8px !important; }
    .payment-info-section { margin-bottom: 8px !important; }
    .invoice-footer { margin-bottom: 6px !important; }
    
    /* Professional text elements */
    .text-muted, small {
        font-size: 7px !important;
        line-height: 1.2 !important;
    }
    
    /* Professional badges and icons */
    .badge, .fa, i {
        font-size: 7px !important;
    }
    
    /* Professional text wrapping */
    .item-desc {
        word-wrap: break-word !important;
        word-break: break-word !important;
        hyphens: auto !important;
    }
    
    /* Emphasize total row */
    .total-row {
        font-size: 10px !important;
        font-weight: bold !important;
    }
    
    /* Professional line breaks */
    br {
        line-height: 1.1 !important;
    }
    
    /* Ensure proper positioning */
    .invoice-container {
        width: 100vw !important;
        height: 100vh !important;
        max-height: 100vh !important;
        display: block !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 9999 !important;
    }
    
    /* Keep essential elements visible */
    .work-description {
        max-height: 25px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    
    /* Limit payment section height but keep visible */
    .payment-info-section {
        max-height: 60px !important;
        overflow: hidden !important;
    }
    
    .banking-details {
        max-height: 55px !important;
        overflow: hidden !important;
    }
    
    .payment-terms {
        max-height: 55px !important;
        overflow: hidden !important;
    }
}

/* Professional A4 page settings */
@page {
    size: A4 portrait !important;
    margin: 4mm !important; /* Professional margins */
    padding: 0 !important;
}

/* Emergency measures for very long content */
@media print {
    /* If still too long, make minor adjustments */
    .invoice-container.long-content {
        transform: scale(0.75) !important;
    }
    
    .invoice-container.very-long-content {
        transform: scale(0.72) !important;
    }
    
    /* Ensure colors print properly */
    .invoice-table thead tr,
    .total-row,
    .invoice-header {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

<script>
// Professional optimized print function
function optimizedPrint() {
    console.log('Starting professional print optimization...');
    
    // Hide non-essential elements
    const elementsToHide = document.querySelectorAll('.no-print, .action-buttons, nav, header, footer, .invoice-actions');
    elementsToHide.forEach(el => {
        el.style.display = 'none';
        el.style.visibility = 'hidden';
    });
    
    // Add professional print class
    document.body.classList.add('professional-print-optimized');
    document.documentElement.classList.add('professional-print-optimized');
    
    // Smart content optimization
    const container = document.querySelector('.invoice-container');
    if (container) {
        // Measure content height and apply appropriate scaling
        const contentHeight = container.scrollHeight;
        const viewportHeight = window.innerHeight;
        
        console.log('Content height:', contentHeight, 'Viewport height:', viewportHeight);
        
        // Apply scaling based on content length
        if (contentHeight > viewportHeight * 1.1) {
            container.classList.add('very-long-content');
            console.log('Applied very-long-content scaling');
        } else if (contentHeight > viewportHeight * 0.95) {
            container.classList.add('long-content');
            console.log('Applied long-content scaling');
        }
        
        // Professional styling adjustments
        container.style.transform = container.classList.contains('very-long-content') ? 'scale(0.72)' : 
                                   container.classList.contains('long-content') ? 'scale(0.75)' : 'scale(0.78)';
        container.style.transformOrigin = 'top left';
        container.style.position = 'fixed';
        container.style.top = '0';
        container.style.left = '0';
        container.style.width = '100vw';
        container.style.height = '100vh';
        container.style.maxHeight = '100vh';
        container.style.overflow = 'hidden';
        container.style.zIndex = '9999';
        
        // Smart content truncation - only if absolutely necessary
        const workDesc = container.querySelector('.work-description');
        if (workDesc && workDesc.scrollHeight > 30) {
            workDesc.style.maxHeight = '25px';
            workDesc.style.overflow = 'hidden';
            workDesc.style.textOverflow = 'ellipsis';
        }
    }
    
    // Print with professional timing
    setTimeout(() => {
        console.log('Printing professional version...');
        window.print();
        
        // Clean up after printing
        setTimeout(() => {
            console.log('Cleaning up professional print...');
            document.body.classList.remove('professional-print-optimized');
            document.documentElement.classList.remove('professional-print-optimized');
            elementsToHide.forEach(el => {
                el.style.display = '';
                el.style.visibility = '';
            });
            
            // Reset container styles
            if (container) {
                container.classList.remove('long-content', 'very-long-content');
                container.style.transform = '';
                container.style.transformOrigin = '';
                container.style.position = '';
                container.style.top = '';
                container.style.left = '';
                container.style.width = '';
                container.style.height = '';
                container.style.maxHeight = '';
                container.style.overflow = '';
                container.style.zIndex = '';
                
                // Restore work description
                const workDesc = container.querySelector('.work-description');
                if (workDesc) {
                    workDesc.style.maxHeight = '';
                    workDesc.style.overflow = '';
                    workDesc.style.textOverflow = '';
                }
            }
        }, 2000);
    }, 400);
}

// Update the print button
document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.querySelector('.print-btn');
    if (printBtn) {
        printBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            optimizedPrint();
        });
        
        // Update button text
        printBtn.innerHTML = '<i class="fas fa-print"></i> Print Professional Invoice';
    }
});

// Professional print optimization styles
const professionalPrintStyle = document.createElement('style');
professionalPrintStyle.textContent = `
    @media print {
        .professional-print-optimized {
            overflow: hidden !important;
            height: 100vh !important;
            max-height: 100vh !important;
        }
        
        .professional-print-optimized .invoice-container {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 9999 !important;
            width: 100vw !important;
        }
        
        .professional-print-optimized * {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        
        /* Maintain professional appearance */
        .professional-print-optimized .company-name {
            color: #007bff !important;
        }
        
        .professional-print-optimized .invoice-table thead tr {
            background: #007bff !important;
            color: white !important;
        }
        
        .professional-print-optimized .total-row {
            background: #007bff !important;
            color: white !important;
        }
        
        .professional-print-optimized .work-done-section {
            background: #e8f4fd !important;
            border-left: 3px solid #007bff !important;
        }
    }
`;
document.head.appendChild(professionalPrintStyle);
</script>