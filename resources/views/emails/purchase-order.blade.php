{{-- filepath: resources/views/emails/purchase-order.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background: #f8f9fa; padding: 20px; border-bottom: 2px solid #007bff; }
        .content { padding: 20px; }
        .po-details { background: #f8f9fa; padding: 15px; margin: 20px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background: #007bff; color: white; }
        .totals { background: #f8f9fa; padding: 15px; margin: 20px 0; }
        .footer { background: #f8f9fa; padding: 20px; border-top: 1px solid #ddd; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Purchase Order</h1>
        <p><strong>PO Number:</strong> {{ $purchaseOrder->po_number }}</p>
        <p><strong>Order Date:</strong> {{ $purchaseOrder->created_at->format('d F Y') }}</p>
    </div>

    <div class="content">
        <p>Dear {{ $purchaseOrder->supplier->name ?? 'Supplier' }},</p>
        
        <p>Please find our purchase order details below. We kindly request you to confirm receipt and provide an estimated delivery date.</p>

        <div class="po-details">
            <h3>Order Details</h3>
            <p><strong>PO Number:</strong> {{ $purchaseOrder->po_number }}</p>
            <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('d F Y') : $purchaseOrder->created_at->format('d F Y') }}</p>
            @if($purchaseOrder->expected_delivery_date)
                <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date->format('d F Y') }}</p>
            @endif
            @if($purchaseOrder->notes)
                <p><strong>Special Instructions:</strong> {{ $purchaseOrder->notes }}</p>
            @endif
        </div>

        <h3>Items Ordered</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Description</th>
                    <th>Item Code</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_description ?: $item->item_name }}</td>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ number_format($item->quantity_ordered, 2) }} {{ $item->unit_of_measure }}</td>
                        <td>R {{ number_format($item->unit_price, 2) }}</td>
                        <td>R {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p><strong>Subtotal:</strong> R {{ number_format($purchaseOrder->total_amount, 2) }}</p>
            <p><strong>VAT (15%):</strong> R {{ number_format($purchaseOrder->vat_amount, 2) }}</p>
            <p><strong>Grand Total:</strong> R {{ number_format($purchaseOrder->grand_total, 2) }}</p>
        </div>

        <h3>Delivery Information</h3>
        <p>
            <strong>Deliver to:</strong><br>
            Your Company Name<br>
            123 Business Street<br>
            City, Province, 0000<br>
            South Africa
        </p>

        <p>Please confirm receipt of this order and provide your estimated delivery schedule.</p>
        
        <p>Thank you for your service.</p>
        
        <p>Best regards,<br>
        <strong>Procurement Team</strong><br>
        Your Company Name</p>
    </div>

    <div class="footer">
        <p>This is an automatically generated email. Please contact us at orders@yourcompany.com if you have any questions.</p>
        <p>PO Reference: {{ $purchaseOrder->po_number }} | Generated: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>