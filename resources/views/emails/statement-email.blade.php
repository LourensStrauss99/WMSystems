<h2>Your Account Statement</h2>
<p>Dear {{ $customer->name ?? 'Customer' }},</p>
<p>Please find attached your latest account statement from {{ $company->name ?? 'Workflow Management System' }}.</p>
<p>If you have any questions, please contact us at info@wmsystems.co.za.</p>
<p>Thank you,<br>{{ $company->name ?? 'Workflow Management System' }}</p> 