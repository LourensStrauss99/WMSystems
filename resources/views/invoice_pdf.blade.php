{{-- filepath: resources/views/invoice_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $jobcard->jobcard_number }}</title>
    <style>
        /* PDF-Optimized Styles - Fix Width Issues */
        @page {
            margin: 10mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            background: white;
            width: 100%;
        }

        .invoice-container {
            width: 100%;
            max-width: 190mm; /* FIXED: Ensure it fits A4 width */
            margin: 0 auto;
            background: white;
            padding: 5mm;
        }

        /* Header Layout - FIXED WIDTH MANAGEMENT */
        .invoice-header {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff;
            overflow: hidden;
        }

        .header-left {
            float: left;
            width: 58%; /* REDUCED from 60% */
        }

        .header-right {
            float: right;
            width: 40%; /* KEEP 40% */
            padding-left: 15px; /* REDUCED padding */
        }

        .company-info {
            width: 100%;
            overflow: hidden;
        }

        .company-logo {
            float: left;
            width: 80px; /* FIXED WIDTH */
            margin-right: 10px;
        }

        .company-logo img {
            max-width: 70px; /* SMALLER */
            max-height: 60px; /* SMALLER */
            display: block;
        }

        .company-details {
            margin-left: 90px; /* Account for logo width + margin */
        }

        .company-name {
            font-size: 20px; /* SMALLER */
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
            line-height: 1.1;
        }

        .company-address, .company-contact {
            font-size: 10px; /* SMALLER */
            color: #666;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        /* Banking Details - Right Side FIXED */
        .banking-details-header {
            background: #f8f9fa;
            padding: 12px; /* REDUCED */
            border-radius: 6px;
            border-left: 3px solid #007bff;
            width: 100%;
            box-sizing: border-box;
        }

        .banking-details-header h3 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .banking-details-header .bank-info {
            font-size: 9px; /* SMALLER */
            line-height: 1.3;
        }

        .bank-row {
            margin-bottom: 3px;
            word-break: break-word;
        }

        .bank-row strong {
            color: #333;
            font-weight: 600;
        }

        /* Clear float fix */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Invoice Details Header - FIXED */
        .invoice-details-header {
            width: 100%;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            overflow: hidden;
        }

        .client-details {
            float: left;
            width: 48%;
            padding-right: 15px;
        }

        .invoice-meta {
            float: right;
            width: 48%;
            text-align: right;
        }

        .client-details h3, .invoice-meta h3 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .client-info {
            font-size: 10px;
            line-height: 1.4;
        }

        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }

        .invoice-date, .due-date {
            margin-bottom: 6px;
            font-size: 10px;
        }

        /* Work Done Section */
        .work-done-section {
            margin-bottom: 20px;
            background: #e8f4fd;
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid #007bff;
        }

        .work-done-section h3 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .work-description {
            font-size: 10px;
            line-height: 1.4;
            color: #444;
        }

        /* Invoice Table - FIXED */
        .invoice-table-section {
            margin-bottom: 20px;
        }

        .invoice-table-section h3 {
            color: #007bff;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 600;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
            table-layout: fixed; /* FIXED: Prevent table overflow */
        }

        .invoice-table thead tr {
            background: #007bff;
            color: white;
        }

        .invoice-table th {
            padding: 8px 4px; /* REDUCED padding */
            text-align: left;
            font-weight: 600;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }

        .invoice-table th.qty, 
        .invoice-table th.unit-price, 
        .invoice-table th.total,
        .invoice-table td.qty, 
        .invoice-table td.unit-price, 
        .invoice-table td.total {
            text-align: right;
            width: 15%; /* ADJUSTED */
        }

        .invoice-table th.item-desc,
        .invoice-table td.item-desc {
            width: 55%; /* ADJUSTED */
        }

        .invoice-table td {
            padding: 8px 4px; /* REDUCED padding */
            border: 1px solid #dee2e6;
            vertical-align: top;
            font-size: 10px;
            word-wrap: break-word;
        }

        .item-row:nth-child(even) {
            background: #f8f9fa;
        }

        .section-header td {
            background: #e9ecef !important;
            font-weight: bold;
            color: #495057;
            padding: 6px 4px;
            font-size: 10px;
        }

        .subtotal-row, .vat-row {
            background: #f8f9fa;
            font-weight: 600;
        }

        .total-row {
            background: #007bff;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        .subtotal-label, .vat-label, .total-label {
            text-align: right;
            padding: 8px 4px;
        }

        .subtotal-amount, .vat-amount, .total-amount {
            text-align: right;
            padding: 8px 4px;
        }

        .totals-spacer {
            border: none;
            padding: 6px;
        }

        /* Payment Information Section - FIXED */
        .payment-info-section {
            width: 100%;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .banking-details-footer, .payment-terms {
            float: left;
            width: 48%; /* REDUCED from 50% */
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .banking-details-footer {
            margin-right: 4%; /* 2% on each side = 4% gap */
        }

        .banking-details-footer h3, .payment-terms h3 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .bank-info-footer, .terms-content {
            font-size: 10px;
            line-height: 1.4;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 15px;
            font-style: italic;
            color: #666;
            font-size: 10px;
        }

        /* Utility Classes */
        .text-muted {
            color: #666 !important;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Invoice Header - FIXED LAYOUT -->
        <div class="invoice-header clearfix">
            <div class="header-left">
                <div class="company-info clearfix">
                    <div class="company-logo">
                        @if(!empty($company->company_logo))
                            <img src="{{ public_path('storage/' . $company->company_logo) }}" alt="{{ $company->company_name }}">
                        @else
                            <img src="{{ public_path('silogo.jpg') }}" alt="Company Logo">
                        @endif
                    </div>
                    <div class="company-details">
                        <div class="company-name">{{ $company->company_name }}</div>
                        <div class="company-address">
                            {{ $company->address }}<br>
                            {{ $company->city }}, {{ $company->province }} {{ $company->postal_code }}<br>
                            {{ $company->country }}
                        </div>
                        <div class="company-contact">
                            Tel: {{ $company->company_telephone }}<br>
                            Email: {{ $company->company_email }}
                            @if($company->company_website)
                                <br>Web: {{ $company->company_website }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <div class="banking-details-header">
                    <h3>Banking Details</h3>
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
            </div>
        </div>

        <!-- Invoice Details Header - FIXED -->
        <div class="invoice-details-header clearfix">
            <div class="client-details">
                <h3>Invoice To:</h3>
                <div class="client-info">
                    <strong>{{ $jobcard->client->name }}</strong><br>
                    @if($jobcard->client->address)
                        {{ $jobcard->client->address }}<br>
                    @endif
                    @if($jobcard->client->email)
                        Email: {{ $jobcard->client->email }}<br>
                    @endif
                    @if($jobcard->client->telephone)
                        Tel: {{ $jobcard->client->telephone }}
                    @endif
                </div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-number">
                    Invoice #: {{ $jobcard->jobcard_number }}
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
                <h3>Work Description</h3>
                <div class="work-description">
                    {{ $jobcard->work_done }}
                </div>
            </div>
        @endif

        <!-- Invoice Items Table - FIXED -->
        <div class="invoice-table-section">
            <h3>Invoice Details</h3>
            
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
                    <!-- Inventory Items -->
                    @foreach($jobcard->inventory as $item)
                        @php
                            $lineTotal = $item->pivot->quantity * $item->selling_price;
                        @endphp
                        <tr class="item-row">
                            <td class="item-desc">
                                <strong>{{ $item->name }}</strong>
                                @if($item->short_description)
                                    <br><span class="text-muted">{{ $item->short_description }}</span>
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
                    @if($labourHours > 0)
                        <tr class="section-header">
                            <td colspan="4" class="section-title">Labour Services</td>
                        </tr>
                        <tr class="item-row">
                            <td class="item-desc">
                                <strong>Professional Labour</strong>
                                <br><span class="text-muted">{{ number_format($labourHours, 2) }} hours @ R{{ number_format($company->labour_rate ?? 0, 2) }}/hour</span>
                            </td>
                            <td class="qty">{{ number_format($labourHours, 2) }}</td>
                            <td class="unit-price">R {{ number_format($company->labour_rate ?? 0, 2) }}</td>
                            <td class="total">R {{ number_format($labourTotal, 2) }}</td>
                        </tr>
                    @endif

                    <!-- Totals -->                    
                    <tr class="totals-section">
                        <td colspan="4" class="totals-spacer"></td>
                    </tr>
                    <tr class="subtotal-row">
                        <td colspan="3" class="subtotal-label">Subtotal:</td>
                        <td class="subtotal-amount">R {{ number_format($subtotal, 2) }}</td>
                    </tr>
                    <tr class="vat-row">
                        <td colspan="3" class="vat-label">VAT ({{ $company->vat_percent ?? 15 }}%):</td>
                        <td class="vat-amount">R {{ number_format($vat, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" class="total-label">Total Amount Due:</td>
                        <td class="total-amount">R {{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Information Section - FIXED -->
        <div class="payment-info-section clearfix">
            <div class="banking-details-footer">
                <h3>Banking Details</h3>
                <div class="bank-info-footer">
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
                <h3>Payment Terms</h3>
                <div class="terms-content">
                    {{ $company->invoice_terms ?? 'Payment due within 30 days of invoice date. Thank you for your business!' }}
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
    </div>
</body>
</html>