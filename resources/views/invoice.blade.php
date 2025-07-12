@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-4">
    <!-- Streamlined Inline Search Section -->
    <div class="bg-white p-4 rounded-lg shadow-lg mb-6">
        <form method="GET" action="{{ route('invoice.index') }}" class="space-y-3">
            <!-- Search Title -->
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-search mr-2 text-blue-600"></i>Search & Filter Invoices
                </h3>
                @if(request()->hasAny(['client', 'from', 'to', 'status']))
                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-filter mr-1"></i>Filters Active
                    </span>
                @endif
            </div>
            
            <!-- Inline Search Fields -->
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-3 items-end">
                <!-- Client Name -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user mr-1 text-gray-500"></i>Client Name
                    </label>
                    <input type="text" name="client" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" 
                           placeholder="Search by client name..." 
                           value="{{ request('client') }}">
                </div>
                
                <!-- From Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar-alt mr-1 text-gray-500"></i>From
                    </label>
                    <input type="date" name="from" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" 
                           value="{{ request('from') }}">
                </div>
                
                <!-- To Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar-check mr-1 text-gray-500"></i>To
                    </label>
                    <input type="date" name="to" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" 
                           value="{{ request('to') }}">
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-info-circle mr-1 text-gray-500"></i>Status
                    </label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm">
                        <i class="fas fa-search mr-1"></i>Search
                    </button>
                    <a href="{{ route('invoice.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors text-sm font-medium shadow-sm">
                        <i class="fas fa-times mr-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Enhanced Invoice Table -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list mr-2 text-blue-600"></i>Invoice List
                </h3>
                <div class="text-sm text-gray-600">
                    Showing {{ $jobcards->count() }} of {{ $jobcards->total() }} invoices
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            <i class="fas fa-hashtag mr-1"></i>Invoice #
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                            <i class="fas fa-user mr-1"></i>Client
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                            <i class="fas fa-calendar mr-1"></i>Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                            <i class="fas fa-dollar-sign mr-1"></i>Amount
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            <i class="fas fa-cogs mr-1"></i>Actions
                        </th>
                    </tr>    
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobcards as $jobcard)
                        @php
                            // Get the ACTUAL invoice for this jobcard
                            $invoice = $jobcard->invoice;
                            
                            if ($invoice) {
                                // Use the invoice amount directly
                                $grandTotal = $invoice->amount;
                                $itemCount = $jobcard->inventory->count();
                            } else {
                                // Fallback calculation if no invoice exists
                                $inventoryTotal = $jobcard->inventory->sum(function($item) {
                                    return $item->pivot->quantity * $item->selling_price;
                                });
                                
                                // Calculate labour costs from jobcard enhanced data
                                $company = \App\Models\CompanyDetail::first();
                                $totalLabourCost = floatval($jobcard->total_labour_cost ?? 0);
                                
                                // If no enhanced data, fall back to old calculation
                                if ($totalLabourCost == 0) {
                                    $labourHours = $jobcard->employees->sum(fn($employee) => $employee->pivot->hours_worked ?? 0);
                                    $totalLabourCost = $labourHours * ($company->labour_rate ?? 750);
                                }
                                
                                $subtotal = $inventoryTotal + $totalLabourCost;
                                $vat = $subtotal * (($company->vat_percent ?? 15) / 100);
                                $grandTotal = $subtotal + $vat;
                                $itemCount = $jobcard->inventory->count() + ($totalLabourCost > 0 ? 1 : 0);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer invoice-row" onclick="viewInvoice({{ $jobcard->id }})">
                            <!-- Invoice Number -->
                            <td class="px-4 py-4 whitespace-nowrap w-32">
                                <div class="text-sm font-medium text-gray-900">{{ $jobcard->jobcard_number }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $jobcard->id }}</div>
                            </td>
                            
                            <!-- Client Info -->
                            <td class="px-4 py-4 whitespace-nowrap w-48">
                                <div class="text-sm font-medium text-gray-900">{{ $jobcard->client->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $jobcard->client->email ?? '' }}</div>
                            </td>
                            
                            <!-- Date -->
                            <td class="px-4 py-4 whitespace-nowrap w-28">
                                <div class="text-sm text-gray-900">{{ $jobcard->job_date ?? $jobcard->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $jobcard->created_at->format('H:i') }}</div>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-4 py-4 whitespace-nowrap w-24">
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
                                        'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
                                        'paid' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-credit-card'],
                                        'cancelled' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-times-circle'],
                                        'invoiced' => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-file-invoice'],
                                    ];
                                    $config = $statusConfig[$jobcard->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-question'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
                                    <i class="{{ $config['icon'] }} mr-1"></i>
                                    {{ ucfirst($jobcard->status) }}
                                </span>
                            </td>
                            
                            <!-- Amount -->
                            <td class="px-4 py-4 whitespace-nowrap text-right w-24">
                                <div class="text-sm font-medium text-gray-900">R {{ number_format($grandTotal, 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $itemCount }} items</div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-4 py-4 whitespace-nowrap text-center w-32">
                                <div class="flex justify-center space-x-1">
                                    <a href="{{ route('invoice.show', $jobcard->id) }}" 
                                       class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition-all duration-300 text-xs font-semibold shadow-lg"
                                       onclick="event.stopPropagation()" 
                                       title="View Invoice Details">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                    <button class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 transition-all duration-300 text-xs font-semibold shadow-lg"
                                            onclick="event.stopPropagation(); emailInvoice({{ $jobcard->id }})"
                                            title="Email Invoice to Client">
                                        <i class="fas fa-envelope mr-1"></i>Email
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-file-invoice text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
                                    <p class="text-gray-500 mb-4">
                                        @if(request()->hasAny(['client', 'from', 'to', 'status']))
                                            No invoices match your current filter criteria.
                                        @else
                                            You haven't created any invoices yet.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['client', 'from', 'to', 'status']))
                                        <a href="{{ route('invoice.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-times mr-2"></i>Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($jobcards->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $jobcards->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function viewInvoice(invoiceId) {
    // Add loading state
    const btn = event.target.closest('a');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';
        btn.style.opacity = '0.7';
    }
    
    window.location.href = `/invoice/${invoiceId}`;
}

function emailInvoice(invoiceId) {
    if (confirm('📧 Send this invoice via email to the client?')) {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        
        // Show loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sending...';
        btn.disabled = true;
        btn.style.opacity = '0.7';
        
        fetch(`/invoice/${invoiceId}/email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success state
                btn.innerHTML = '<i class="fas fa-check mr-1"></i>Sent!';
                btn.style.background = 'linear-gradient(135deg, #4caf50 0%, #66bb6a 100%)';
                
                // Show success message
                showNotification('✅ Invoice sent successfully!', 'success');
                
                // Reset button after 3 seconds
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.background = '';
                }, 3000);
            } else {
                // Error state
                btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Failed';
                btn.style.background = 'linear-gradient(135deg, #f44336 0%, #ef5350 100%)';
                
                showNotification('❌ Error sending invoice: ' + data.message, 'error');
                
                // Reset button after 3 seconds
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.background = '';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Error';
            btn.style.background = 'linear-gradient(135deg, #f44336 0%, #ef5350 100%)';
            
            showNotification('❌ Network error sending invoice', 'error');
            
            // Reset button after 3 seconds
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.background = '';
            }, 3000);
        });
    }
}

// Enhanced notification function
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.custom-notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `custom-notification fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg font-bold opacity-70 hover:opacity-100">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Slide in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}
</script>

<style>
/* Enhanced Invoice Row Styling */
.invoice-row:hover {
    background: linear-gradient(135deg, #e3f2fd 0%, #f8f9ff 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.15);
    border-left: 4px solid #2196f3;
}

.invoice-row {
    transition: all 0.3s ease;
    border-radius: 8px;
    margin: 2px 0;
}

/* Enhanced Table Styling */
.bg-white {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
}

/* Table Header Enhancement */
.bg-gray-50 {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    color: #1565c0 !important;
    font-weight: 600;
}

/* Status Badge Enhancements */
.bg-yellow-100 {
    background: linear-gradient(135deg, #fff9c4 0%, #fff59d 100%) !important;
    border: 1px solid #ffeb3b;
}

.bg-green-100 {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%) !important;
    border: 1px solid #4caf50;
}

.bg-blue-100 {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    border: 1px solid #2196f3;
}

.bg-red-100 {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%) !important;
    border: 1px solid #f44336;
}

.bg-purple-100 {
    background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%) !important;
    border: 1px solid #9c27b0;
}

/* Button Enhancements */
.bg-blue-500 {
    background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%) !important;
    box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
    border: none;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.bg-blue-500:hover {
    background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%) !important;
    box-shadow: 0 6px 20px rgba(25, 118, 210, 0.4);
    transform: translateY(-2px);
}

.bg-green-500 {
    background: linear-gradient(135deg, #388e3c 0%, #66bb6a 100%) !important;
    box-shadow: 0 4px 15px rgba(56, 142, 60, 0.3);
    border: none;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.bg-green-500:hover {
    background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%) !important;
    box-shadow: 0 6px 20px rgba(56, 142, 60, 0.4);
    transform: translateY(-2px);
}

/* Search Button Enhancement */
.bg-blue-600 {
    background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%) !important;
    box-shadow: 0 4px 15px rgba(25, 118, 210, 0.4);
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.bg-blue-600:hover {
    background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%) !important;
    box-shadow: 0 6px 20px rgba(25, 118, 210, 0.5);
    transform: translateY(-2px);
}

/* Clear Button */
.bg-gray-500 {
    background: linear-gradient(135deg, #757575 0%, #9e9e9e 100%) !important;
    box-shadow: 0 4px 15px rgba(117, 117, 117, 0.3);
}

.bg-gray-500:hover {
    background: linear-gradient(135deg, #616161 0%, #757575 100%) !important;
    transform: translateY(-2px);
}

/* Enhanced focus states */
input:focus, select:focus {
    ring-color: #42a5f5 !important;
    border-color: #1976d2 !important;
    box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.3) !important;
}

/* Filter Active Badge */
.bg-blue-500.text-white {
    background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%) !important;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.3);
    font-weight: 600;
}
</style>
@endsection