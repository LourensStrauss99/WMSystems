{{-- filepath: resources/views/customer-show.blade.php --}}
@extends('layouts.auth')

@section('content')
<div class="container-fluid mt-3">
    <!-- Header with Back Button -->
    <div class="customer-header mb-4">
        <div class="header-left">
            <a href="{{ route('customers.index') }}" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
                </svg>
                Back to Customers
            </a>
            <h2 class="customer-title">{{ $customer->name }} {{ $customer->surname }}</h2>
            <span class="customer-status active">Active Client</span>
        </div>
        <div class="header-actions">
            <button class="action-btn edit" onclick="editCustomer()">✏️ Edit</button>
            <button class="action-btn invoice" onclick="makePayment()">💰 Payments</button>
            <button class="action-btn contact" onclick="contactCustomer()">📞 Contact</button>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="customer-dashboard">
        
        <!-- Customer Information Card -->
        <div class="dashboard-card customer-info">
            <div class="card-header">
                <h5>👤 Customer Information</h5>
                <button class="card-btn" onclick="editCustomerInfo()">✏️</button>
            </div>
            <div class="card-content">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value">{{ $customer->name }} {{ $customer->surname }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account #</span>
                        <span class="info-value">#{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">
                            @if($customer->telephone)
                                <a href="tel:{{ $customer->telephone }}">{{ $customer->telephone }}</a>
                            @else
                                Not provided
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Client Since</span>
                        <span class="info-value">{{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'Not available' }}</span>
                    </div>
                    <div class="info-item full-width">
                        <span class="info-label">Email</span>
                        <span class="info-value">
                            @if($customer->email)
                                <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                            @else
                                Not provided
                            @endif
                        </span>
                    </div>
                    <div class="info-item full-width">
                        <span class="info-label">Address</span>
                        <span class="info-value">{{ $customer->address ?: 'Not provided' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Reference</span>
                        <span class="info-value payment-ref">
                            @if($customer->payment_reference)
                                {{ $customer->payment_reference }}
                                <button class="copy-ref-btn" onclick="copyReference('{{ $customer->payment_reference }}')" title="Copy to clipboard">📋</button>
                            @else
                                <span class="text-muted">Not generated</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item full-width">
                        <span class="info-label">Profile Notes</span>
                        <div class="notes-section">
                            <textarea id="customer-notes" placeholder="Add notes about customer preferences, past interactions, etc.">{{ $customer->notes ?? '' }}</textarea>
                            <button class="save-notes-btn" onclick="saveNotes()">💾 Save Notes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Summary Card -->
        <div class="dashboard-card account-summary">
            <div class="card-header">
                <h5>📊 Account Summary</h5>
                <span class="summary-period">All Time</span>
            </div>
            <div class="card-content">
                <div class="summary-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $customer->total_jobs }}</span>
                        <span class="stat-label">Total Jobs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">R{{ number_format($customer->total_invoiced, 2) }}</span>
                        <span class="stat-label">Total Invoiced</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">R{{ number_format($customer->outstanding_amount, 2) }}</span>
                        <span class="stat-label">Outstanding</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $customer->paid_invoices_count }}</span>
                        <span class="stat-label">Paid Invoices</span>
                    </div>
                </div>
                
                <!-- Quick Payment Status Overview -->
                <div class="payment-health">
                    @php
                        $avgDays = $customer->invoices->avg(function($invoice) {
                            return $invoice->payment_date
                                ? \Carbon\Carbon::parse($invoice->payment_date)->diffInDays($invoice->invoice_date)
                                : \Carbon\Carbon::now()->diffInDays($invoice->invoice_date);
                        });
                        $healthColor = $avgDays <= 30 ? 'good' : ($avgDays <= 60 ? 'warning' : 'poor');
                    @endphp
                    <div class="health-indicator {{ $healthColor }}">
                        <span class="health-label">Payment Health</span>
                        <span class="health-value">{{ number_format($avgDays ?? 0, 0) }} days avg</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Done History -->
        <div class="dashboard-card work-history">
            <div class="card-header">
                <h5>🔧 Work Done History</h5>
                <div class="card-actions">
                    <button class="card-btn" onclick="filterWork()">🔍</button>
                    <button class="card-btn" onclick="addWork()">➕</button>
                </div>
            </div>
            <div class="card-content">
                <div class="work-timeline">
                    @forelse($workHistory as $jobcard)
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $jobcard->status ?? 'completed' }}"></div>
                            <div class="timeline-content">
                                <div class="work-header">
                                    <h6 class="work-title">Work Done - {{ $jobcard->created_at->format('M d, Y') }}</h6>
                                    <span class="work-date">{{ $jobcard->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="work-details">
                                    <p class="work-description">{{ $jobcard->work_done ?: 'No details provided' }}</p>
                                    @if($jobcard->work_request)
                                        <div class="work-request">
                                            <strong>Original Request:</strong> {{ Str::limit($jobcard->work_request, 100) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="work-meta">
                                    <span class="work-status {{ $jobcard->status ?? 'completed' }}">
                                        {{ ucfirst($jobcard->status ?? 'Completed') }}
                                    </span>
                                    @if($jobcard->jobcard_number)
                                        <span class="work-number">Job #{{ $jobcard->jobcard_number }}</span>
                                    @endif
                                </div>
                                <div class="work-actions">
                                    <button class="mini-btn" onclick="viewJobcard({{ $jobcard->id }})">👁️ View</button>
                                    @if(($jobcard->status ?? 'completed') !== 'invoiced')
                                        <button class="mini-btn" onclick="editJobcard({{ $jobcard->id }})">✏️ Edit</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="empty-icon">🔧</span>
                            <p>No work history found</p>
                            <button class="empty-action-btn" onclick="createJobcard()">Create First Jobcard</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Invoice & Payment History -->
        <div class="dashboard-card payment-history">
            <div class="card-header">
                <h5>💰 Invoice & Payment History</h5>
                <div class="card-actions">
                    <button class="card-btn" onclick="toggleView()" id="view-toggle">📊 Payments</button>
                    <button class="card-btn" onclick="exportStatement()">📄 Export</button>
                    <button class="card-btn" onclick="sendStatement()">📧 Statement</button>
                </div>
            </div>
            <div class="card-content">
                
                <!-- View Toggle Tabs -->
                <div class="view-tabs">
                    <button class="tab-btn active" onclick="showInvoices()" id="invoices-tab">🧾 Invoices</button>
                    <button class="tab-btn" onclick="showPayments()" id="payments-tab">💳 Payments</button>
                    <button class="tab-btn" onclick="showCombined()" id="combined-tab">📋 Combined</button>
                </div>

                <!-- Invoices View -->
                <div id="invoices-view" class="history-view active">
                    <div class="invoice-table-container">
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Days Due</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoiceHistory as $invoice)
                                    @php
                                        $invoiceDate = \Carbon\Carbon::parse($invoice->invoice_date);
                                        $dueDate = $invoiceDate->addDays(30); // Assuming 30 days payment terms
                                        $daysDue = $invoice->status === 'paid' 
                                            ? 0 
                                            : \Carbon\Carbon::now()->diffInDays($dueDate, false);
                                        
                                        if ($invoice->status === 'paid') {
                                            $dueClass = 'paid';
                                        } elseif ($daysDue > 0) {
                                            $dueClass = 'not-due';
                                        } elseif ($daysDue >= -30) {
                                            $dueClass = 'overdue-30';
                                        } elseif ($daysDue >= -60) {
                                            $dueClass = 'overdue-60';
                                        } elseif ($daysDue >= -90) {
                                            $dueClass = 'overdue-90';
                                        } else {
                                            $dueClass = 'overdue-120';
                                        }
                                    @endphp
                                    <tr class="invoice-row">
                                        <td>
                                            <a href="#" class="invoice-link" onclick="viewInvoice('{{ $invoice->invoice_number }}')">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $invoiceDate->format('M d, Y') }}</td>
                                        <td class="amount">R{{ number_format($invoice->amount, 2) }}</td>
                                        <td>
                                            <span class="status-badge {{ strtolower($invoice->status) }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $dueDate->format('M d, Y') }}</td>
                                        <td class="days-due {{ $dueClass }}">
                                            @if($invoice->status === 'paid')
                                                <span class="due-badge paid">Paid</span>
                                            @elseif($daysDue > 0)
                                                <span class="due-badge not-due">{{ $daysDue }} days</span>
                                            @else
                                                <span class="due-badge overdue">{{ abs($daysDue) }} overdue</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="mini-btn" onclick="viewInvoice('{{ $invoice->invoice_number }}')" title="View">👁️</button>
                                                @if(strtolower($invoice->status) === 'unpaid')
                                                    <button class="mini-btn payment" onclick="recordPayment('{{ $invoice->invoice_number }}')" title="Record Payment">💳</button>
                                                    <button class="mini-btn email" onclick="emailInvoice('{{ $invoice->invoice_number }}')" title="Email Invoice">📧</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="empty-invoices">
                                            <div class="empty-state">
                                                <span class="empty-icon">💰</span>
                                                <p>No invoices found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payments View -->
                <div id="payments-view" class="history-view">
                    <div class="payments-table-container">
                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>For Invoice</th>
                                    <th>Reference</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentHistory ?? [] as $payment)
                                    <tr class="payment-row">
                                        <td>
                                            <a href="{{ route('payments.receipt', $payment->id) }}" class="payment-link" target="_blank">
                                                {{ $payment->receipt_number }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                        <td class="amount positive">R{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            <span class="method-badge {{ $payment->payment_method }}">
                                                @switch($payment->payment_method)
                                                    @case('cash') 💵 Cash @break
                                                    @case('card') 💳 Card @break
                                                    @case('eft') 🏦 EFT @break
                                                    @case('cheque') 📝 Cheque @break
                                                    @case('payfast') 📱 PayFast @break
                                                    @default 💰 {{ ucfirst($payment->payment_method) }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            @if($payment->invoice_jobcard_number)
                                                <span class="invoice-ref">{{ $payment->invoice_jobcard_number }}</span>
                                            @else
                                                <span class="text-muted">General Payment</span>
                                            @endif
                                        </td>
                                        <td class="reference">
                                            @if($payment->reference_number)
                                                <span class="ref-number">{{ $payment->reference_number }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="mini-btn receipt" onclick="viewReceipt({{ $payment->id }})" title="View Receipt">🧾</button>
                                                <button class="mini-btn print" onclick="printReceipt({{ $payment->id }})" title="Print Receipt">🖨️</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="empty-payments">
                                            <div class="empty-state">
                                                <span class="empty-icon">💳</span>
                                                <p>No payments recorded</p>
                                                <button class="empty-action-btn" onclick="makePayment()">Record First Payment</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Combined Timeline View -->
                <div id="combined-view" class="history-view">
                    <div class="timeline-container">
                        @php
                            $combined = collect();
                            
                            // Add invoices to timeline
                            foreach($invoiceHistory as $invoice) {
                                $combined->push([
                                    'type' => 'invoice',
                                    'date' => $invoice->invoice_date,
                                    'data' => $invoice
                                ]);
                            }
                            
                            // Add payments to timeline
                            foreach($paymentHistory ?? [] as $payment) {
                                $combined->push([
                                    'type' => 'payment',
                                    'date' => $payment->payment_date,
                                    'data' => $payment
                                ]);
                            }
                            
                            // Sort by date descending
                            $combined = $combined->sortByDesc('date');
                        @endphp

                        @forelse($combined as $item)
                            <div class="timeline-entry {{ $item['type'] }}">
                                <div class="timeline-icon">
                                    @if($item['type'] === 'invoice')
                                        🧾
                                    @else
                                        💳
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    @if($item['type'] === 'invoice')
                                        <div class="entry-header">
                                            <h6>Invoice {{ $item['data']->invoice_number }}</h6>
                                            <span class="entry-date">{{ \Carbon\Carbon::parse($item['data']->invoice_date)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="entry-details">
                                            <span class="amount">R{{ number_format($item['data']->amount, 2) }}</span>
                                            <span class="status-badge {{ strtolower($item['data']->status) }}">{{ ucfirst($item['data']->status) }}</span>
                                        </div>
                                    @else
                                        <div class="entry-header">
                                            <h6>Payment {{ $item['data']->receipt_number }}</h6>
                                            <span class="entry-date">{{ \Carbon\Carbon::parse($item['data']->payment_date)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="entry-details">
                                            <span class="amount positive">+R{{ number_format($item['data']->amount, 2) }}</span>
                                            <span class="method-badge {{ $item['data']->payment_method }}">{{ ucfirst($item['data']->payment_method) }}</span>
                                            @if($item['data']->invoice_jobcard_number)
                                                <span class="for-invoice">for {{ $item['data']->invoice_jobcard_number }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <span class="empty-icon">📋</span>
                                <p>No transaction history</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enhanced Payment Summary -->
                @if(($invoiceHistory && $invoiceHistory->count() > 0) || ($paymentHistory && $paymentHistory->count() > 0))
                    <div class="enhanced-payment-summary">
                        <div class="summary-grid">
                            <div class="summary-section invoices">
                                <h6>📄 Invoices</h6>
                                <div class="summary-row">
                                    <span>Total Invoiced:</span>
                                    <span class="amount">R{{ number_format(collect($invoiceHistory)->sum('amount'), 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Total Paid:</span>
                                    <span class="amount positive">R{{ number_format(collect($invoiceHistory)->sum('paid_amount'), 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Outstanding:</span>
                                    <span class="amount outstanding">R{{ number_format(collect($invoiceHistory)->sum('outstanding_amount'), 2) }}</span>
                                </div>
                                <div class="summary-row aging">
                                    <span>Overdue (30+ days):</span>
                                    <span class="amount overdue">R{{ number_format(collect($invoiceHistory)->where('age_category', '!=', 'current')->where('age_category', '!=', 'paid')->sum('outstanding_amount'), 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="summary-section payments">
                                <h6>💳 Payment Activity</h6>
                                <div class="summary-row">
                                    <span>This Month:</span>
                                    <span class="amount positive">R{{ number_format(collect($paymentHistory ?? [])->where('payment_date', '>=', now()->startOfMonth())->sum('amount'), 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Last Payment:</span>
                                    <span class="amount">
                                        @if(isset($paymentSummary['recent_payment']) && $paymentSummary['recent_payment'])
                                            R{{ number_format($paymentSummary['recent_payment']->amount, 2) }}
                                            <small>({{ \Carbon\Carbon::parse($paymentSummary['recent_payment']->payment_date)->diffForHumans() }})</small>
                                        @else
                                            None
                                        @endif
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span>Payment Methods:</span>
                                    <div class="payment-methods-summary">
                                        @php $methods = collect($paymentHistory ?? [])->groupBy('payment_method'); @endphp
                                        @foreach($methods as $method => $payments)
                                            <small class="method-summary">{{ ucfirst($method) }}: R{{ number_format($payments->sum('amount'), 2) }}</small>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="summary-section balance">
                                <h6>⚖️ Account Status</h6>
                                @php
                                    $totalInvoiced = collect($invoiceHistory)->sum('amount');
                                    $totalPaid = collect($invoiceHistory)->sum('paid_amount');
                                    $totalOutstanding = collect($invoiceHistory)->sum('outstanding_amount');
                                    $overdueAmount = collect($invoiceHistory)->where('age_category', '!=', 'current')->where('age_category', '!=', 'paid')->sum('outstanding_amount');
                                @endphp
                                
                                <div class="summary-row total">
                                    <span>Current Balance:</span>
                                    <span class="amount {{ $totalOutstanding > 0 ? 'outstanding' : 'positive' }}">
                                        R{{ number_format($totalOutstanding, 2) }}
                                    </span>
                                </div>
                                
                                @if($overdueAmount > 0)
                                    <div class="summary-row overdue-alert">
                                        <span>⚠️ Overdue:</span>
                                        <span class="amount overdue">R{{ number_format($overdueAmount, 2) }}</span>
                                    </div>
                                @endif
                                
                                <div class="account-health">
                                    @php
                                        $healthScore = $totalInvoiced > 0 ? (($totalPaid / $totalInvoiced) * 100) : 100;
                                        $healthColor = $healthScore >= 80 ? 'good' : ($healthScore >= 60 ? 'fair' : 'poor');
                                    @endphp
                                    <div class="health-indicator {{ $healthColor }}">
                                        <span class="health-bar" style="width: {{ $healthScore }}%"></span>
                                    </div>
                                    <small>Payment Health: {{ number_format($healthScore, 1) }}%</small>
                                </div>
                                
                                @if($totalOutstanding > 0)
                                    <div class="balance-actions">
                                        <button class="balance-btn" onclick="makePayment()">💳 Take Payment</button>
                                        @if($overdueAmount > 0)
                                            <button class="balance-btn urgent" onclick="sendReminder()">⚠️ Send Reminder</button>
                                        @else
                                            <button class="balance-btn" onclick="sendStatement()">📧 Send Statement</button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Aging Analysis -->
                        @if(isset($agingSummary) && is_array($agingSummary) && array_sum($agingSummary) > 0)
                            <div class="aging-analysis">
                                <h6>📊 Aging Analysis</h6>
                                <div class="aging-bars">
                                    @php
                                        $agingData = [
                                            'current' => ['label' => 'Current', 'amount' => $agingSummary['current'], 'class' => 'current'],
                                            '30_days' => ['label' => '1-30 Days', 'amount' => $agingSummary['30_days'], 'class' => 'days-30'],
                                            '60_days' => ['label' => '31-60 Days', 'amount' => $agingSummary['60_days'], 'class' => 'days-60'],
                                            '90_days' => ['label' => '61-90 Days', 'amount' => $agingSummary['90_days'], 'class' => 'days-90'],
                                            '120_days' => ['label' => '90+ Days', 'amount' => $agingSummary['120_days'], 'class' => 'days-120']
                                        ];
                                        $maxAmount = collect($agingSummary)->max();
                                    @endphp
                                    
                                    @foreach($agingData as $period => $data)
                                        @if($data['amount'] > 0)
                                            <div class="aging-bar-item">
                                                <div class="aging-label">
                                                    <span class="period-name">{{ $data['label'] }}</span>
                                                    <span class="period-amount">R{{ number_format($data['amount'], 2) }}</span>
                                                </div>
                                                <div class="aging-bar-track">
                                                    <div class="aging-bar-fill {{ $data['class'] }}" 
                                                         style="width: {{ $maxAmount > 0 ? ($data['amount'] / $maxAmount) * 100 : 0 }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Quick Action Modal -->
<div id="quick-modal" class="quick-modal">
    <div class="quick-modal-content">
        <div class="quick-modal-header">
            <h5 id="modal-title">Quick Action</h5>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <div class="quick-modal-body" id="modal-content">
            <!-- Dynamic content -->
        </div>
    </div>
</div>

<script>
function editCustomer() {
    window.location.href = '/client/{{ $customer->id }}/edit';
}

function makePayment() {
    window.location.href = '/client/{{ $customer->id }}/payments/create';
}

function contactCustomer() {
    const phone = '{{ $customer->telephone }}';
    const email = '{{ $customer->email }}';
    
    const modal = document.getElementById('modal-content');
    modal.innerHTML = `
        <div class="contact-options">
            <h6>Contact Options</h6>
            ${phone ? `<button class="contact-btn" onclick="window.open('tel:${phone}')">📞 Call ${phone}</button>` : ''}
            ${email ? `<button class="contact-btn" onclick="window.open('mailto:${email}')">📧 Email ${email}</button>` : ''}
            <button class="contact-btn" onclick="sendSMS()">💬 Send SMS</button>
        </div>
    `;
    document.getElementById('modal-title').textContent = 'Contact Customer';
    document.getElementById('quick-modal').style.display = 'flex';
}

function saveNotes() {
    const notes = document.getElementById('customer-notes').value;
    
    fetch('/client/{{ $customer->id }}/notes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notes saved successfully');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving notes');
    });
}

function viewJobcard(id) {
    // Integrate with your jobcard viewing route
    alert(`View jobcard ${id}`);
}

function editJobcard(id) {
    // Integrate with your jobcard editing route
    alert(`Edit jobcard ${id}`);
}

function createJobcard() {
    // Integrate with your jobcard creation route
    alert('Create new jobcard');
}

function viewInvoice(invoiceNumber) {
    // Integrate with your invoice viewing route
    alert(`View invoice ${invoiceNumber}`);
}

function recordPayment(invoiceNumber) {
    window.location.href = '/client/{{ $customer->id }}/payments/create?invoice=' + invoiceNumber;
}

function emailInvoice(invoiceNumber) {
    alert(`Email invoice ${invoiceNumber}`);
}

function closeModal() {
    document.getElementById('quick-modal').style.display = 'none';
}

function submitPayment(event, invoiceNumber) {
    event.preventDefault();
    // Add your payment recording functionality here
    alert('Payment recorded successfully');
    closeModal();
}

function copyReference(reference) {
    navigator.clipboard.writeText(reference).then(function() {
        alert('Payment reference copied: ' + reference);
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = reference;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Payment reference copied: ' + reference);
    });
}

// View toggle functions
function showInvoices() {
    document.querySelectorAll('.history-view').forEach(view => view.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('invoices-view').classList.add('active');
    document.getElementById('invoices-tab').classList.add('active');
}

function showPayments() {
    document.querySelectorAll('.history-view').forEach(view => view.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('payments-view').classList.add('active');
    document.getElementById('payments-tab').classList.add('active');
}

function showCombined() {
    document.querySelectorAll('.history-view').forEach(view => view.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('combined-view').classList.add('active');
    document.getElementById('combined-tab').classList.add('active');
}

function toggleView() {
    const currentView = document.querySelector('.history-view.active').id;
    if (currentView === 'invoices-view') {
        showPayments();
    } else if (currentView === 'payments-view') {
        showCombined();
    } else {
        showInvoices();
    }
}

// Payment-related functions
function viewReceipt(paymentId) {
    window.open('/payments/' + paymentId + '/receipt', '_blank');
}

function printReceipt(paymentId) {
    window.open('/payments/' + paymentId + '/receipt?print=1', '_blank');
}

function exportStatement() {
    alert('Export statement functionality - to be implemented');
}

function sendStatement() {
    alert('Send statement functionality - to be implemented');
}

function sendReminder() {
    alert('Send payment reminder functionality - to be implemented');
}
</script>

<style>
/* Customer Header */
.customer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.back-btn {
    background: #6c757d;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9em;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.back-btn:hover {
    background: #5a6268;
    text-decoration: none;
    color: white;
    transform: translateY(-1px);
}

.customer-title {
    margin: 0;
    color: #495057;
    font-size: 1.8em;
    font-weight: 600;
}

.customer-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
}

.customer-status.active {
    background: #d4edda;
    color: #155724;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-btn.edit {
    background: #007bff;
    color: white;
}

.action-btn.invoice {
    background: #28a745;
    color: white;
}

.action-btn.contact {
    background: #17a2b8;
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Dashboard Grid */
.customer-dashboard {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto 1fr;
    gap: 20px;
    min-height: 70vh;
}

.customer-info {
    grid-column: 1;
    grid-row: 1;
}

.account-summary {
    grid-column: 2;
    grid-row: 1;
}

.work-history {
    grid-column: 1;
    grid-row: 2 / 4;
}

.payment-history {
    grid-column: 2;
    grid-row: 2 / 4;
}

/* Dashboard Cards */
.dashboard-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.08);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.card-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    font-size: 1.1em;
    font-weight: 600;
    color: #495057;
}

.card-actions {
    display: flex;
    gap: 6px;
}

.card-btn {
    background: #fff;
    border: 1px solid #dee2e6;
    padding: 6px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8em;
    transition: all 0.2s ease;
}

.card-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.card-content {
    padding: 20px;
    flex: 1;
    overflow-y: auto;
}

/* Customer Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-label {
    font-size: 0.8em;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
}

.info-value {
    font-size: 0.95em;
    color: #495057;
    font-weight: 500;
}

.info-value a {
    color: #007bff;
    text-decoration: none;
}

.info-value a:hover {
    text-decoration: underline;
}

.notes-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.notes-section textarea {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 8px;
    font-size: 0.9em;
    resize: vertical;
    min-height: 60px;
}

.save-notes-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.8em;
    cursor: pointer;
}

/* Account Summary */
.summary-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-number {
    display: block;
    font-size: 1.5em;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.8em;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
}

.payment-health {
    text-align: center;
}

.health-indicator {
    padding: 10px;
    border-radius: 8px;
    display: inline-block;
}

.health-indicator.good {
    background: #d4edda;
    color: #155724;
}

.health-indicator.warning {
    background: #fff3cd;
    color: #856404;
}

.health-indicator.poor {
    background: #f8d7da;
    color: #721c24;
}

.health-label {
    display: block;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
}

.health-value {
    display: block;
    font-size: 1.1em;
    font-weight: 700;
}

/* Work Timeline */
.work-timeline {
    position: relative;
    padding-left: 30px;
}

.work-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-dot {
    position: absolute;
    left: -24px;
    top: 8px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-dot.assigned { background: #6c757d; }
.timeline-dot.in-progress { background: #fd7e14; }
.timeline-dot.completed { background: #28a745; }
.timeline-dot.invoiced { background: #007bff; }

.timeline-content {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
}

.work-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.work-title {
    margin: 0;
    font-size: 1em;
    font-weight: 600;
    color: #495057;
}

.work-date {
    font-size: 0.8em;
    color: #6c757d;
}

.work-description {
    font-size: 0.9em;
    color: #495057;
    margin-bottom: 8px;
}

.work-request {
    font-size: 0.85em;
    color: #6c757d;
    background: #fff;
    padding: 8px;
    border-radius: 4px;
    margin-bottom: 8px;
}

.work-meta {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.work-status {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75em;
    font-weight: 600;
    text-transform: capitalize;
}

.work-status.assigned { background: #6c757d; color: white; }
.work-status.in-progress { background: #fd7e14; color: white; }
.work-status.completed { background: #28a745; color: white; }
.work-status.invoiced { background: #007bff; color: white; }

.work-number {
    font-size: 0.75em;
    color: #6c757d;
    background: #fff;
    padding: 3px 6px;
    border-radius: 4px;
}

.work-actions {
    display: flex;
    gap: 6px;
}

.mini-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.7em;
    cursor: pointer;
}

.mini-btn:hover {
    background: #0056b3;
}

.mini-btn.payment {
    background: #28a745;
}

.mini-btn.email {
    background: #17a2b8;
}

/* Invoice Table */
.invoice-table-container {
    overflow-x: auto;
    margin-bottom: 20px;
}

.invoice-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9em;
}

.invoice-table th,
.invoice-table td {
    padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.invoice-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    font-size: 0.8em;
    text-transform: uppercase;
}

.invoice-link {
    color: #007bff;
    text-decoration: none;
    font-weight: 600;
}

.invoice-link:hover {
    text-decoration: underline;
}

.amount {
    font-weight: 600;
    text-align: right;
}

.status-badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.7em;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.paid {
    background: #d4edda;
    color: #155724;
}

.status-badge.unpaid {
    background: #f8d7da;
    color: #721c24;
}

/* Credit Age Styling */
.credit-age {
    text-align: center;
}

.age-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75em;
    font-weight: 600;
    display: inline-block;
}

.age-good .age-badge {
    background: #d4edda;
    color: #155724;
}

.age-warning .age-badge {
    background: #fff3cd;
    color: #856404;
}

.age-danger .age-badge {
    background: #f8d7da;
    color: #721c24;
}

.age-critical .age-badge {
    background: #dc3545;
    color: white;
}

.age-overdue .age-badge {
    background: #343a40;
    color: #dc3545;
    font-weight: 700;
}

.action-buttons {
    display: flex;
    gap: 4px;
}

/* Payment Summary */
.payment-summary {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9em;
}

.summary-row.total {
    border-top: 1px solid #dee2e6;
    padding-top: 8px;
    font-weight: 600;
    font-size: 1em;
}

.amount.paid {
    color: #28a745;
}

.amount.outstanding {
    color: #dc3545;
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-icon {
    font-size: 2.5em;
    display: block;
    margin-bottom: 10px;
    opacity: 0.5;
}

.empty-state p {
    margin-bottom: 15px;
    font-style: italic;
}

.empty-action-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
}

/* Quick Modal */
.quick-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.quick-modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
}

.quick-modal-header {
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.quick-modal-body {
    padding: 20px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
}

.contact-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.contact-btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    text-align: left;
    transition: all 0.2s ease;
}

.contact-btn:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.payment-form .form-group {
    margin-bottom: 15px;
}

.payment-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #495057;
}

.payment-form .form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 0.9em;
}

.submit-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
}

/* New Styles for Payment Reference and Portal */
.payment-ref {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #007bff;
}

.copy-ref-btn, .copy-mini-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.8em;
    padding: 2px 4px;
    border-radius: 3px;
    transition: background 0.2s;
}

.copy-ref-btn:hover, .copy-mini-btn:hover {
    background: #f0f0f0;
}

.payment-portal {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.payment-portal h6 {
    margin-bottom: 12px;
    color: #495057;
}

.portal-info {
    margin-bottom: 12px;
}

.ref-code {
    background: #e3f0fb;
    color: #007bff;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 700;
    font-size: 0.9em;
}

.portal-actions {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.portal-btn {
    padding: 6px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 0.8em;
    cursor: pointer;
    transition: all 0.2s;
}

.portal-btn.disabled {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}

.setup-notice {
    color: #6c757d;
    font-style: italic;
}

/* View Tabs */
.view-tabs {
    display: flex;
    gap: 2px;
    margin-bottom: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    padding: 4px;
}

.tab-btn {
    flex: 1;
    background: transparent;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #6c757d;
}

.tab-btn.active {
    background: #007bff;
    color: white;
}

.tab-btn:hover:not(.active) {
    background: #e9ecef;
}

/* History Views */
.history-view {
    display: none;
}

.history-view.active {
    display: block;
}

/* Payments Table */
.payments-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9em;
}

.payments-table th,
.payments-table td {
    padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.payments-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    font-size: 0.8em;
    text-transform: uppercase;
}

.payment-link {
    color: #28a745;
    text-decoration: none;
    font-weight: 600;
}

.payment-link:hover {
    text-decoration: underline;
}

.amount.positive {
    color: #28a745;
}

/* Method Badges */
.method-badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.7em;
    font-weight: 600;
    display: inline-block;
}

.method-badge.cash {
    background: #d4edda;
    color: #155724;
}

.method-badge.card {
    background: #cce7ff;
    color: #004085;
}

.method-badge.eft {
    background: #e2e3e5;
    color: #383d41;
}

.method-badge.cheque {
    background: #fff3cd;
    color: #856404;
}

.method-badge.payfast {
    background: #d1ecf1;
    color: #0c5460;
}

/* Due Date Styling */
.days-due {
    text-align: center;
}

.due-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7em;
    font-weight: 600;
    display: inline-block;
}

.due-badge.paid {
    background: #d4edda;
    color: #155724;
}

.due-badge.not-due {
    background: #e2e3e5;
    color: #495057;
}

.due-badge.overdue {
    background: #f8d7da;
    color: #721c24;
}

.overdue-30 .due-badge.overdue {
    background: #ffeaa7;
    color: #856404;
}

.overdue-60 .due-badge.overdue {
    background: #fdcb6e;
    color: #8b4513;
}

.overdue-90 .due-badge.overdue {
    background: #e17055;
    color: white;
}

.overdue-120 .due-badge.overdue {
    background: #d63031;
    color: white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* Combined Timeline */
.timeline-container {
    max-height: 400px;
    overflow-y: auto;
}

.timeline-entry {
    display: flex;
    gap: 15px;
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
    align-items: flex-start;
}

.timeline-entry:last-child {
    border-bottom: none;
}

.timeline-entry.invoice {
    background: #f8f9ff;
}

.timeline-entry.payment {
    background: #f0fff4;
}

.timeline-icon {
    font-size: 1.2em;
    width: 30px;
    text-align: center;
    margin-top: 2px;
}

.timeline-content {
    flex: 1;
}

.entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.entry-header h6 {
    margin: 0;
    font-size: 0.9em;
    font-weight: 600;
}

.entry-date {
    font-size: 0.8em;
    color: #6c757d;
}

.entry-details {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.for-invoice {
    font-size: 0.75em;
    color: #6c757d;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
}

/* Enhanced Payment Summary */
.enhanced-payment-summary {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
}

.summary-section {
    padding: 15px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.summary-section h6 {
    margin: 0 0 10px 0;
    font-size: 0.9em;
    font-weight: 600;
    color: #495057;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 5px;
}

.summary-section .summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 0.85em;
}

.summary-section .summary-row.total {
    border-top: 1px solid #dee2e6;
    padding-top: 8px;
    margin-top: 8px;
    font-weight: 600;
    font-size: 0.9em;
}

.balance-actions {
    margin-top: 10px;
    display: flex;
    gap: 6px;
}

.balance-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 0.7em;
    cursor: pointer;
}

.balance-btn:hover {
    background: #0056b3;
}

.reference {
    font-family: 'Courier New', monospace;
    font-size: 0.8em;
}

.ref-number {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    color: #495057;
}

.invoice-ref {
    color: #007bff;
    font-weight: 600;
    font-size: 0.8em;
}

/* Action Button Enhancements */
.mini-btn.receipt {
    background: #28a745;
}

.mini-btn.print {
    background: #6c757d;
}

/* Enhanced Summary Styles */
.payment-methods-summary {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.method-summary {
    font-size: 0.7em;
    color: #6c757d;
}

.summary-row.overdue-alert {
    background: #fff5f5;
    padding: 4px 8px;
    border-radius: 4px;
    border-left: 3px solid #dc3545;
    margin: 4px 0;
}

.amount.overdue {
    color: #dc3545;
    font-weight: 700;
}

/* Account Health Indicator */
.account-health {
    margin-top: 10px;
}

.health-indicator {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 4px;
}

.health-bar {
    height: 100%;
    transition: width 0.3s ease;
}

.health-indicator.good .health-bar {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.health-indicator.fair .health-bar {
    background: linear-gradient(90deg, #ffc107, #fd7e14);
}

.health-indicator.poor .health-bar {
    background: linear-gradient(90deg, #dc3545, #e74c3c);
}

.balance-btn.urgent {
    background: #dc3545;
    animation: pulse-urgent 2s infinite;
}

@keyframes pulse-urgent {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

/* Aging Analysis */
.aging-analysis {
    margin-top: 20px;
    padding: 15px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
}

.aging-analysis h6 {
    margin: 0 0 15px 0;
    font-size: 0.9em;
    font-weight: 600;
    color: #495057;
}

.aging-bars {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.aging-bar-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.aging-label {
    min-width: 120px;
    display: flex;
    justify-content: space-between;
    font-size: 0.8em;
}

.period-name {
    color: #495057;
}

.period-amount {
    font-weight: 600;
    color: #28a745;
}

.aging-bar-track {
    flex: 1;
    height: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.aging-bar-fill {
    height: 100%;
    transition: width 0.5s ease;
    border-radius: 10px;
}

.aging-bar-fill.current {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.aging-bar-fill.days-30 {
    background: linear-gradient(90deg, #ffc107, #ffca2c);
}

.aging-bar-fill.days-60 {
    background: linear-gradient(90deg, #fd7e14, #ff8c42);
}

.aging-bar-fill.days-90 {
    background: linear-gradient(90deg, #dc3545, #e55353);
}

.aging-bar-fill.days-120 {
    background: linear-gradient(90deg, #6f42c1, #8e44ad);
}

/* Status badges for partial payments */
.status-badge.partial {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* Responsive updates */
@media (max-width: 768px) {
    .aging-bar-item {
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
    }
    
    .aging-label {
        min-width: auto;
    }
}
</style>
@endsection