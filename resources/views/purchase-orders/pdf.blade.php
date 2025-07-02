{{-- filepath: resources/views/purchase-orders/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .po-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .address-section {
            margin: 30px 0;
        }
        
        .address-box {
            float: left;
            width: 45%;
            margin-right: 5%;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .totals-section {
            float: right;
            width: 40%;
            margin-top: 30px;
        }
        
        .totals-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .totals-table td {
            padding: 5px;
            border: none;
        }
        
        .total-row {
            border-top: 2px solid #333;
            font-weight: bold;
        }
        
        .terms-section {
            float: left;
            width: 55%;
            margin-top: 30px;
        }
        
        .terms-box {
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
            margin-bottom: 15px;
        }
        
        .footer-section {
            clear: both;
            margin-top: 60px;
            border-top: 1px solid #ddd;
            padding-top: 30px;
        }
        
        .signature-box {
            float: left;
            width: 30%;
            margin-right: 5%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin: 40px 20px 10px 20px;
        }
        
        .footer-text {
            clear: both;
            text-align: center;
            margin-top: 30px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header clearfix">
        <div class="company-info">
            <div class="company-name">Your Company Name</div>
            <div>
                123 Business Street<br>
                City, Province, 0000<br>
                Tel: +27 11 123 4567<br>
                Email: orders@yourcompany.com
            </div>
        </div>
        <div class="po-info">
            <div class="document-title">PURCHASE ORDER</div>
            <div>
                <strong>PO Number:</strong> {{ $purchaseOrder->po_number }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($purchaseOrder->created_at)->format('d F Y') }}<br>
                <strong>Status:</strong> Draft
            </div>
        </div>
    </div>

    <!-- Address Section -->
    <div class="address-section clearfix">
        <div class="address-box">
            <div class="section-title">Ship To:</div>
            <strong>Your Company Name</strong><br>
            123 Business Street<br>
            City, Province, 0000<br>
            South Africa
        </div>
        <div class="address-box">
            <div class="section-title">Supplier:</div>
            <strong>{{ $purchaseOrder->supplier_name }}</strong><br>
            @if($purchaseOrder->supplier_contact)
                Attn: {{ $purchaseOrder->supplier_contact }}<br>
            @endif
            @if($purchaseOrder->supplier_address)
                {{ $purchaseOrder->supplier_address }}<br>
            @endif
            @if($purchaseOrder->supplier_email)
                Email: {{ $purchaseOrder->supplier_email }}<br>
            @endif
            @if($purchaseOrder->supplier_phone)
                Tel: {{ $purchaseOrder->supplier_phone }}<br>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="25%">Item Description</th>
                <th width="12%">Item Code</th>
                <th width="10%">Qty</th>
                <th width="12%">Unit Price</th>
                <th width="12%">Line Total</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @if($items && $items->count() > 0)
                @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->item_description)
                                <br><small>{{ $item->item_description }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->item_code ?: '-' }}</td>
                        <td class="text-center">{{ number_format($item->quantity_ordered, 0) }}</td>
                        <td class="text-right">R {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right"><strong>R {{ number_format($item->line_total, 2) }}</strong></td>
                        <td class="text-center">{{ ucfirst($item->status) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Terms and Totals -->
    <div class="clearfix">
        <div class="terms-section">
            @if($purchaseOrder->terms_conditions)
                <div class="section-title">Special Instructions:</div>
                <div class="terms-box">
                    {{ $purchaseOrder->terms_conditions }}
                </div>
            @endif
            
            <div class="section-title">Payment Terms:</div>
            <div class="terms-box">
                {{ $purchaseOrder->payment_terms ?: '30 days from invoice date' }}
            </div>
        </div>
        
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">R {{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>VAT (15%):</strong></td>
                    <td class="text-right">R {{ number_format($vatAmount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>R {{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Prepared By<br>{{ auth()->user()->name ?? 'System User' }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Approved By<br>Manager</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Date<br>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
        </div>
        
        <div class="footer-text">
            This purchase order is valid for 30 days from the date of issue.
        </div>
    </div>
</body>
</html>