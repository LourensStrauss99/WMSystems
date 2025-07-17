{{-- filepath: resources/views/invoice_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $jobcard->jobcard_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #f4f6fa;
            margin: 0;
            padding: 0;
        }
        .invoice-container {
            max-width: 700px;
            margin: 20px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.08);
            padding: 40px 32px 32px 32px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1976d2;
            padding-bottom: 18px;
        }
        .company-info {
            font-size: 1.1em;
        }
        .company-logo {
            height: 60px;
        }
        .invoice-title {
            color: #1976d2;
            font-size: 2em;
            font-weight: 700;
            margin: 0;
        }
        .section {
            margin-top: 32px;
        }
        .section-title {
            font-size: 1.1em;
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 8px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .details-table th, .details-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e3e8ee;
            text-align: left;
        }
        .details-table th {
            background: #f4f8fb;
            font-weight: 600;
            color: #1976d2;
        }
        .details-table tr:nth-child(even) td {
            background: #fafbfc;
        }
        .total-row td {
            font-weight: 700;
            background: #1976d2;
            color: #fff;
            font-size: 1.1em;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 32px;
        }
        .summary-box {
            background: #f4f8fb;
            border-radius: 8px;
            padding: 18px 20px;
            width: 48%;
            font-size: 0.98em;
        }
        .badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 12px;
            font-size: 0.95em;
            font-weight: 600;
            color: #fff;
            background: #43a047;
            margin-left: 8px;
        }
        .badge.unpaid { background: #e53935; }
        .badge.paid { background: #43a047; }
        .badge.overdue { background: #fbc02d; color: #222; }
        .footer {
            text-align: center;
            color: #aaa;
            font-size: 0.95em;
            margin-top: 32px;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="header">
        <div>
            @if(isset($company) && $company->logo)
                <img src="{{ public_path($company->logo) }}" alt="Company Logo" class="company-logo"><br>
            @endif
            <div class="company-info">
                <strong>{{ $company->name ?? 'Company Name' }}</strong><br>
                {{ $company->address ?? '' }}<br>
                {{ $company->city ?? '' }}<br>
                {{ $company->country ?? '' }}<br>
                {{ $company->telephone ?? '' }} | {{ $company->email ?? '' }}
            </div>
        </div>
        <div style="text-align:right;">
            <div class="invoice-title">INVOICE</div>
            <div>
                <span style="color:#888;">Status:</span>
                @php
                    $badgeClass = 'unpaid';
                    if(isset($invoice) && $invoice->status === 'paid') $badgeClass = 'paid';
                    elseif(isset($invoice) && $invoice->status === 'overdue') $badgeClass = 'overdue';
                @endphp
                <span class="badge {{ $badgeClass }}">{{ ucfirst($invoice->status ?? 'Unpaid') }}</span>
            </div>
            <div style="margin-top:8px;">
                <strong>Invoice #:</strong> {{ $jobcard->jobcard_number }}<br>
                <strong>Date:</strong> {{ $invoice->invoice_date ?? $jobcard->job_date }}<br>
                <strong>Due:</strong> {{ $invoice->due_date ?? '' }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Billed To</div>
        <div>
            {{ $jobcard->client->name ?? '' }}<br>
            {{ $jobcard->client->address ?? '' }}<br>
            {{ $jobcard->client->email ?? '' }}<br>
            {{ $jobcard->client->telephone ?? '' }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Invoice Details</div>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                </tr>
            </thead>
            <tbody>
                {{-- Inventory Items --}}
                @foreach($jobcard->inventory as $item)
                    <tr>
                        <td>{{ $item->description ?? $item->name }}</td>
                        <td>{{ $item->pivot->quantity ?? 0 }}</td>
                        <td>{{ number_format($item->selling_price ?? $item->sell_price ?? 0, 2) }}</td>
                        <td>{{ number_format(($item->pivot->quantity ?? 0) * ($item->selling_price ?? $item->sell_price ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                @if($jobcard->inventory->count())
                <tr>
                    <td colspan="3" style="text-align:right;">Inventory Subtotal</td>
                    <td>{{ number_format($inventoryTotal ?? $jobcard->getInventoryTotal(), 2) }}</td>
                </tr>
                @endif
                {{-- Labour Services --}}
                @php
                    $company = $company ?? \App\Models\CompanyDetail::first();
                    $labourRows = [];
                    foreach($jobcard->employees as $employee) {
                        $type = $employee->pivot->hour_type ?? 'normal';
                        $hours = $employee->pivot->hours_worked ?? 0;
                        $rate = $company->labour_rate ?? 750;
                        $label = 'Professional Labour';
                        if($type === 'overtime') { $rate *= ($company->overtime_multiplier ?? 1.5); $label = 'Overtime Labour'; }
                        elseif($type === 'weekend') { $rate *= ($company->weekend_multiplier ?? 2.0); $label = 'Weekend Labour'; }
                        elseif($type === 'holiday') { $rate *= ($company->public_holiday_multiplier ?? 2.5); $label = 'Holiday Labour'; }
                        $labourRows[] = [ 'label' => $label, 'hours' => $hours, 'rate' => $rate, 'total' => $hours * $rate ];
                    }
                @endphp
                @foreach($labourRows as $row)
                    @if($row['hours'] > 0)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td>{{ $row['hours'] }}</td>
                        <td>{{ number_format($row['rate'], 2) }}</td>
                        <td>{{ number_format($row['total'], 2) }}</td>
                    </tr>
                    @endif
                @endforeach
                @if($jobcard->call_out_fee > 0)
                <tr>
                    <td>Emergency Call Out Fee</td>
                    <td>1</td>
                    <td>{{ number_format($jobcard->call_out_fee, 2) }}</td>
                    <td>{{ number_format($jobcard->call_out_fee, 2) }}</td>
                </tr>
                @endif
                @if($jobcard->mileage_km > 0)
                <tr>
                    <td>Total / Mileage</td>
                    <td>{{ $jobcard->mileage_km }}</td>
                    <td>{{ number_format($company->mileage_rate ?? 7.50, 2) }}</td>
                    <td>{{ number_format($jobcard->mileage_cost, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" style="text-align:right;">Labour Services Subtotal</td>
                    <td>{{ number_format($jobcard->total_labour_cost ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right;">Subtotal</td>
                    <td>{{ number_format(($inventoryTotal ?? $jobcard->getInventoryTotal()) + ($jobcard->total_labour_cost ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right;">VAT ({{ $company->vat_percent ?? 15 }}%)</td>
                    <td>{{ number_format($vat ?? ((($inventoryTotal ?? $jobcard->getInventoryTotal()) + ($jobcard->total_labour_cost ?? 0)) * (($company->vat_percent ?? 15)/100)), 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Total Amount Due</td>
                    <td>{{ number_format($grandTotal ?? ((($inventoryTotal ?? $jobcard->getInventoryTotal()) + ($jobcard->total_labour_cost ?? 0)) * (1 + ($company->vat_percent ?? 15)/100)), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <div class="summary-box">
            <strong>Banking Details</strong><br>
            Bank: {{ $company->bank_name ?? 'Capitec Bank' }}<br>
            Account Holder: {{ $company->bank_account_holder ?? 'L. Strauss' }}<br>
            Account Number: {{ $company->bank_account_number ?? '1590012345' }}<br>
            Branch Code: {{ $company->bank_branch_code ?? '470010' }}<br>
            SWIFT: {{ $company->bank_swift ?? 'CABLZAJJ' }}
        </div>
        <div class="summary-box">
            <strong>Payment Terms</strong><br>
            Please pay within 30 days of invoice date.<br>
            Late payments are subject to a 2.5% per month charge.<br>
            <br>
            <strong>Notes:</strong><br>
            Thank you for your business!
        </div>
    </div>

    <div class="footer">
        This is a system-generated invoice. If you have any questions, contact us at {{ $company->email ?? 'info@yourcompany.co.za' }}.
    </div>
</div>
</body>
</html>