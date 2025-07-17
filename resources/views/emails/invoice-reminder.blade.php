<h2>Invoice Reminder</h2>
<p>Dear {{ $invoice->client->name ?? 'Customer' }},</p>
<p>This is a friendly reminder that invoice <strong>{{ $invoice->invoice_number }}</strong> is still outstanding.</p>
<p><strong>Amount Due:</strong> R{{ number_format($invoice->amount, 2) }}<br>
<strong>Due Date:</strong> {{ $invoice->due_date ?? 'N/A' }}</p>
<p>Please make payment at your earliest convenience.</p>
<p>Thank you,<br>{{ $company->name ?? 'Workflow Management System' }}</p> 