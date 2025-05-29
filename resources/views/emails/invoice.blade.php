{{-- filepath: resources/views/emails/invoice.blade.php --}}
<div style="background: #fff; padding: 24px; border-radius: 8px; font-family: Arial, sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <img src="{{ $message->embed(public_path('silogo.jpg')) }}" alt="Company Logo" style="max-width: 120px;">
        </div>
        <div style="text-align: right;">
            <h2>{{ $company->company_name }}</h2>
            <div>{{ $company->address }}, {{ $company->city }}, {{ $company->province }}, {{ $company->postal_code }}, {{ $company->country }}</div>
            <div>Tel: {{ $company->company_telephone }} | Email: {{ $company->company_email }}</div>
        </div>
    </div>
    <hr>
    <div style="display: flex; justify-content: space-between;">
        <div>
            <h4>Invoice To:</h4>
            <div>{{ $jobcard->client->name }}</div>
            <div>{{ $jobcard->client->address }}</div>         {{-- Client address --}}
            <div>{{ $jobcard->client->email }}</div>  
            <div>{{ $jobcard->client->telephone }}</div>
        </div>
        <div style="text-align: right;">
            <h4>Invoice #: {{ $jobcard->jobcard_number }}</h4>
            <div>Date: {{ $jobcard->job_date }}</div>
        </div>
    </div>
    <h4 style="margin-top: 24px;">Inventory Used</h4>
    <table width="100%" border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $inventoryTotal = 0; @endphp
            @foreach($jobcard->inventory as $item)
                @php
                    $lineTotal = $item->pivot->quantity * $item->selling_price;
                    $inventoryTotal += $lineTotal;
                @endphp
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->pivot->quantity }}</td>
                    <td>R {{ number_format($item->selling_price, 2) }}</td>
                    <td>R {{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;">Inventory Subtotal</th>
                <th>R {{ number_format($inventoryTotal, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    @php
        $labourHours = $jobcard->time_spent / 60;
        $labourTotal = $labourHours * $company->labour_rate;
        $subtotal = $inventoryTotal + $labourTotal;
        $vat = $subtotal * ($company->vat_percent / 100);
        $grandTotal = $subtotal + $vat;
    @endphp
    <div style="margin-top: 16px;">
        <strong>Labour ({{ number_format($labourHours, 2) }} hrs @ R{{ number_format($company->labour_rate, 2) }}/hr):</strong>
        R {{ number_format($labourTotal, 2) }}
    </div>

    <table width="100%" style="margin-top: 16px;">
        <tr>
            <th style="text-align: right;">Subtotal:</th>
            <td style="text-align: right;">R {{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <th style="text-align: right;">VAT ({{ $company->vat_percent }}%):</th>
            <td style="text-align: right;">R {{ number_format($vat, 2) }}</td>
        </tr>
        <tr>
            <th style="text-align: right;">Total:</th>
            <td style="text-align: right;"><strong>R {{ number_format($grandTotal, 2) }}</strong></td>
        </tr>
    </table>

    <h4 style="margin-top: 24px;">Banking Details</h4>
    <div>Bank: {{ $company->bank_name }}</div>
    <div>Account Holder: {{ $company->account_holder }}</div>
    <div>Account Number: {{ $company->account_number }}</div>
    <div>Branch Code: {{ $company->branch_code }}</div>
    <div>SWIFT/BIC: {{ $company->swift_code }}</div>

    <div style="margin-top: 24px;">
        <strong>Terms:</strong> {{ $company->invoice_terms }}
    </div>
    <div style="margin-top: 8px;">
        <em>{{ $company->invoice_footer }}</em>
    </div>
</div>