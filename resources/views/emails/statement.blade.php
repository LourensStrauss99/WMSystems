<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Statement</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; margin-bottom: 24px; }
        .company { font-size: 1.2em; font-weight: bold; color: #1976d2; }
        .statement-title { font-size: 1.5em; margin: 12px 0; }
        .info-table { width: 100%; margin-bottom: 24px; }
        .info-table td { padding: 4px 8px; }
        .statement-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .statement-table th, .statement-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .statement-table th { background: #f4f8fb; color: #1976d2; }
        .summary { margin-top: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($company->company_logo))
            <div style="margin-bottom:12px;">
                <img src="{{ asset('storage/' . $company->company_logo) }}" alt="Company Logo" style="max-height:60px;">
            </div>
        @endif
        <div class="company" style="font-size:1.3em; font-weight:bold; color:#1976d2;">{{ $company->company_name ?? '' }}</div>
        @if(!empty($company->company_slogan))
            <div style="font-style:italic; margin-bottom:6px;">{{ $company->company_slogan }}</div>
        @endif
        <div style="margin:8px 0; font-size:12px;">
            <span>Reg #: {{ $company->company_reg_number ?? '-' }}</span>
            <span style="margin-left:16px;">VAT #: {{ $company->vat_reg_number ?? '-' }}</span>
        </div>
        <div style="display: flex; justify-content: center; gap: 32px; margin-bottom: 8px;">
            <div style="background:#f4f8fb; border:1px solid #d1e3fa; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:16px 20px; min-width:260px; max-width:320px; text-align:left;">
                <div style="font-weight:600; color:#1976d2; margin-bottom:8px;">Contact & Address</div>
                <div>{{ $company->physical_address ?? $company->address ?? '' }}</div>
                <div>{{ $company->city ?? '' }}{{ !empty($company->province) ? ', ' . $company->province : '' }}{{ !empty($company->postal_code) ? ', ' . $company->postal_code : '' }}</div>
                <div>{{ $company->country ?? '' }}</div>
                <div style="margin-top:8px;">Tel: {{ $company->company_telephone ?? $company->company_cell ?? '-' }}</div>
                <div>Fax: {{ $company->company_fax ?? '-' }}</div>
                <div>Cell: {{ $company->company_cell ?? '-' }}</div>
                <div>Email: {{ $company->company_email ?? '-' }}</div>
                <div>Website: {{ $company->company_website ?? '-' }}</div>
            </div>
            <div style="background:#f4f8fb; border:1px solid #d1e3fa; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:16px 20px; min-width:260px; max-width:320px; text-align:left;">
                <div style="font-weight:600; color:#1976d2; margin-bottom:8px;">Banking Details</div>
                <div>Bank: {{ $company->bank_name ?? '-' }}</div>
                <div>Acc Holder: {{ $company->account_holder ?? '-' }}</div>
                <div>Acc #: {{ $company->account_number ?? '-' }}</div>
                <div>Branch: {{ $company->branch_code ?? '-' }}</div>
                <div>Branch Name: {{ $company->branch_name ?? '-' }}</div>
                <div>SWIFT: {{ $company->swift_code ?? '-' }}</div>
                <div>Type: {{ ucfirst($company->account_type ?? '-') }}</div>
            </div>
        </div>
        <div class="statement-title" style="margin-top:18px;">Account Statement</div>
    </div>
    <table class="info-table">
        <tr>
            <td><strong>Customer:</strong></td>
            <td>{{ $customer->name ?? '' }}</td>
            <td><strong>Date:</strong></td>
            <td>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td><strong>Email:</strong></td>
            <td>{{ $customer->email ?? '' }}</td>
            <td><strong>Account #:</strong></td>
            <td>#{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
    </table>
    <table class="statement-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Paid</th>
                <th>Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer->invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '' }}</td>
                    <td>R{{ number_format($invoice->amount, 2) }}</td>
                    <td>{{ ucfirst($invoice->status) }}</td>
                    <td>R{{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                    <td>R{{ number_format($invoice->outstanding_amount ?? ($invoice->amount - ($invoice->paid_amount ?? 0)), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="summary">
        <div>Total Invoiced: R{{ number_format($customer->invoices->sum('amount'), 2) }}</div>
        <div>Total Paid: R{{ number_format($customer->invoices->sum('paid_amount'), 2) }}</div>
        <div>Total Outstanding: R{{ number_format($customer->invoices->sum(function($inv){return $inv->outstanding_amount ?? ($inv->amount - ($inv->paid_amount ?? 0));}), 2) }}</div>
    </div>
    <div style="margin-top:32px; font-size:12px;">
        <strong>Payment Requirements:</strong>
        <ul style="margin:8px 0 0 16px; padding:0; text-align:left;">
            <li>Please use your account number or payment reference when making payments.</li>
            <li>{{ $company->invoice_terms ?? 'Payments are due within 30 days of invoice date unless otherwise agreed.' }}</li>
            <li>Banking details are provided above for EFT payments.</li>
            <li>For any queries, contact us at {{ $company->company_email ?? $company->accounts_email ?? 'info@company.com' }} or {{ $company->company_telephone ?? $company->company_cell ?? '-' }}.</li>
        </ul>
    </div>
    @if(!empty($company->invoice_footer))
        <div style="margin-top:18px; font-size:11px; color:#555; text-align:center;">{{ $company->invoice_footer }}</div>
    @endif
</body>
</html> 