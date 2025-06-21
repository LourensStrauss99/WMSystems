@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-4">
    <!-- Enhanced Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 rounded-lg shadow-lg mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">📄 Invoice Management</h1>
                <p class="text-blue-100">Track and manage all your submitted invoices</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">{{ $jobcards->total() }}</div>
                <div class="text-blue-100">Total Invoices</div>
            </div>
        </div>
    </div>

    <!-- Enhanced Search and Filter Section -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">
            <i class="fas fa-search mr-2 text-blue-600"></i>Search & Filter Invoices
        </h3>
        
        <form method="GET" action="{{ route('invoice.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user mr-2 text-gray-600"></i>Client Name
                    </label>
                    <input type="text" name="client" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                           placeholder="Search by client name..." 
                           value="{{ request('client') }}">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-600"></i>From Date
                    </label>
                    <input type="date" name="from" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                           value="{{ request('from') }}">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-calendar-check mr-2 text-gray-600"></i>To Date
                    </label>
                    <input type="date" name="to" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                           value="{{ request('to') }}">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-filter mr-2 text-gray-600"></i>Status
                    </label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-between items-center pt-4 border-t">
                <div class="text-sm text-gray-600">
                    @if(request()->hasAny(['client', 'from', 'to', 'status']))
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                            <i class="fas fa-filter mr-1"></i>Filters Active
                        </span>
                    @endif
                </div>
                <div class="space-x-3">
                    <a href="{{ route('invoice.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                        <i class="fas fa-times mr-2"></i>Clear All
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-search mr-2"></i>Search Invoices
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Invoice Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Total Invoices</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $jobcards->total() }}</p>
                </div>
                <i class="fas fa-file-invoice text-4xl text-blue-500"></i>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">This Month</p>
                    <p class="text-3xl font-bold text-green-600">{{ $jobcards->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                </div>
                <i class="fas fa-calendar-month text-4xl text-green-500"></i>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $jobcards->where('status', 'pending')->count() }}</p>
                </div>
                <i class="fas fa-clock text-4xl text-yellow-500"></i>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 font-semibold">Completed</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $jobcards->where('status', 'completed')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl text-purple-500"></i>
            </div>
        </div>
    </div>

    <!-- Enhanced Invoice Table -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
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
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-hashtag mr-1"></i>Invoice #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-user mr-1"></i>Client
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-1"></i>Date
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-dollar-sign mr-1"></i>Amount
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobcards as $jobcard)
                        @php
                            // Calculate totals for display
                            $inventoryTotal = $jobcard->inventory->sum(function($item) {
                                return $item->pivot->quantity * $item->selling_price;
                            });
                            $labourHours = $jobcard->employees->sum(fn($employee) => $employee->pivot->hours_worked ?? 0);
                            $labourTotal = $labourHours * ($company->labour_rate ?? 0);
                            $subtotal = $inventoryTotal + $labourTotal;
                            $vat = $subtotal * (($company->vat_percent ?? 15) / 100);
                            $grandTotal = $subtotal + $vat;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer invoice-row" onclick="viewInvoice({{ $jobcard->id }})">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $jobcard->jobcard_number }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $jobcard->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $jobcard->client->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $jobcard->client->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $jobcard->job_date ?? $jobcard->created_at->format('Y-m-d') }}</div>
                                <div class="text-sm text-gray-500">{{ $jobcard->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
                                        'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
                                        'paid' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-credit-card'],
                                        'cancelled' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-times-circle'],
                                    ];
                                    $config = $statusConfig[$jobcard->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-question'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
                                    <i class="{{ $config['icon'] }} mr-1"></i>
                                    {{ ucfirst($jobcard->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">R {{ number_format($grandTotal, 2) }}</div>
                                <div class="text-sm text-gray-500">{{ $jobcard->inventory->count() }} items</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('invoice.show', $jobcard->id) }}" 
                                       class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all duration-300 text-xs font-semibold shadow-lg"
                                       onclick="event.stopPropagation()" 
                                       title="View Invoice Details">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                    <button class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all duration-300 text-xs font-semibold shadow-lg"
                                            onclick="event.stopPropagation(); emailInvoice({{ $jobcard->id }})"
                                            title="Email Invoice to Client">
                                        <i class="fas fa-envelope mr-1"></i>Email
                                    </button>
                                    <button class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-all duration-300 text-xs font-semibold shadow-lg"
                                            onclick="event.stopPropagation(); printInvoice({{ $jobcard->id }})"
                                            title="Print Invoice">
                                        <i class="fas fa-print mr-1"></i>Print
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

function printInvoice(invoiceId) {
    // Open invoice in new window and print
    const printWindow = window.open(`/invoice/${invoiceId}`, '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
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

// Enhanced auto-refresh with visual feedback
let refreshCount = 0;
setInterval(() => {
    refreshCount++;
    const url = new URL(window.location);
    url.searchParams.set('auto_refresh', '1');
    
    // Add subtle loading indicator
    const header = document.querySelector('.bg-gradient-to-r');
    if (header) {
        header.style.opacity = '0.9';
        header.style.transform = 'scale(0.998)';
    }
    
    fetch(url.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update counts if they've changed
        if (data.stats) {
            console.log(`Auto-refresh ${refreshCount}: Stats updated`, data.stats);
        }
        
        // Reset header
        if (header) {
            header.style.opacity = '1';
            header.style.transform = 'scale(1)';
        }
    })
    .catch(error => {
        console.log('Auto-refresh failed:', error);
        
        // Reset header
        if (header) {
            header.style.opacity = '1';
            header.style.transform = 'scale(1)';
        }
    });
}, 30000);

// Enhanced row click with better feedback
document.addEventListener('DOMContentLoaded', function() {
    // Add click animation to all action buttons
    document.querySelectorAll('.bg-blue-500, .bg-green-500, .bg-purple-500').forEach(button => {
        button.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Enhanced print function with A4 optimization
function optimizedPrint() {
    // Hide any elements that might cause overflow
    const elementsToHide = document.querySelectorAll('.no-print');
    elementsToHide.forEach(el => el.style.display = 'none');
    
    // Add print optimization class
    document.body.classList.add('print-optimized');
    
    // Print with slight delay to ensure styles are applied
    setTimeout(() => {
        window.print();
        
        // Clean up after printing
        setTimeout(() => {
            document.body.classList.remove('print-optimized');
            elementsToHide.forEach(el => el.style.display = '');
        }, 1000);
    }, 100);
}

// Update the print button to use optimized print
document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.querySelector('.print-btn');
    if (printBtn) {
        printBtn.addEventListener('click', function(e) {
            e.preventDefault();
            optimizedPrint();
        });
    }
});

// Print optimization styles
const printOptimizationStyle = document.createElement('style');
printOptimizationStyle.textContent = `
    .print-optimized {
        overflow: hidden !important;
    }
    
    .print-optimized .invoice-container {
        max-height: 100vh !important;
        overflow: hidden !important;
    }
    
    @media print {
        .print-optimized .invoice-container {
            transform: scale(0.85) !important;
            transform-origin: top left !important;
            height: 100vh !important;
            overflow: hidden !important;
        }
    }
`;
document.head.appendChild(printOptimizationStyle);
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

/* Header Enhancement */
.bg-gradient-to-r {
    background: linear-gradient(135deg, #1976d2 0%, #42a5f5 50%, #64b5f6 100%);
}

/* Statistics Cards Enhancement */
.border-l-4 {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.border-l-4:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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

.bg-purple-500 {
    background: linear-gradient(135deg, #7b1fa2 0%, #ab47bc 100%) !important;
    box-shadow: 0 4px 15px rgba(123, 31, 162, 0.3);
    border: none;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.bg-purple-500:hover {
    background: linear-gradient(135deg, #6a1b9a 0%, #7b1fa2 100%) !important;
    box-shadow: 0 6px 20px rgba(123, 31, 162, 0.4);
    transform: translateY(-2px);
}

/* Enhanced Search Section */
.bg-white.p-6.rounded-lg.shadow-lg.mb-6 {
    background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%);
    border: 1px solid #e3f2fd;
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

/* Filter Active Badge */
.bg-blue-100.text-blue-800 {
    background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%) !important;
    color: white !important;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.3);
}

/* Clear All Button */
.bg-gray-500 {
    background: linear-gradient(135deg, #757575 0%, #9e9e9e 100%) !important;
    box-shadow: 0 4px 15px rgba(117, 117, 117, 0.3);
}

.bg-gray-500:hover {
    background: linear-gradient(135deg, #616161 0%, #757575 100%) !important;
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

/* Loading animation enhancement */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { 
        opacity: 1; 
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    }
    50% { 
        opacity: .7; 
        background: linear-gradient(135deg, #bbdefb 0%, #90caf9 100%);
    }
}

/* Custom scrollbar enhancement */
.overflow-x-auto::-webkit-scrollbar {
    height: 10px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
    border-radius: 5px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #42a5f5 0%, #1976d2 100%);
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
}

/* Enhanced focus states */
input:focus, select:focus {
    ring-color: #42a5f5 !important;
    border-color: #1976d2 !important;
    box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.3) !important;
}

/* Row click enhancement */
tbody tr {
    cursor: pointer;
    position: relative;
}

tbody tr::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 0;
    background: linear-gradient(135deg, #42a5f5 0%, #1976d2 100%);
    transition: width 0.3s ease;
    z-index: 1;
}

tbody tr:hover::before {
    width: 4px;
}

/* Action buttons spacing */
.space-x-2 > * + * {
    margin-left: 8px;
}

/* Enhanced table cell padding */
.px-6.py-4 {
    padding: 16px 24px;
}

/* Better text contrast */
.text-gray-900 {
    color: #0d47a1 !important;
    font-weight: 600;
}

.text-gray-500 {
    color: #1565c0 !important;
}

/* Statistics cards icons */
.fa-file-invoice, .fa-calendar-month, .fa-clock, .fa-check-circle {
    opacity: 0.8;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

/* Enhanced Print Styles for Single A4 Page */
@media print {
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-width: 0 !important;
        font-size: 10px !important; /* Reduced font size for better fit */
        line-height: 1.3 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .invoice-container {
        box-shadow: none !important;
        padding: 8mm !important; /* Reduced padding */
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100vh !important;
        overflow: hidden !important;
        page-break-inside: avoid !important;
        transform: scale(0.85) !important; /* Scale down content */
        transform-origin: top left !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    /* Compact header */
    .invoice-header {
        page-break-inside: avoid !important;
        margin-bottom: 15px !important;
        padding-bottom: 10px !important;
    }
    
    .company-name {
        font-size: 20px !important;
        margin-bottom: 5px !important;
    }
    
    .company-logo .logo {
        max-width: 80px !important;
        max-height: 60px !important;
    }
    
    .company-address, .company-contact {
        font-size: 9px !important;
        margin-bottom: 3px !important;
    }
    
    /* Compact invoice details header */
    .invoice-details-header {
        margin-bottom: 15px !important;
        padding: 10px !important;
        font-size: 9px !important;
    }
    
    .invoice-details-header h3 {
        font-size: 12px !important;
        margin-bottom: 8px !important;
    }
    
    .invoice-number {
        font-size: 14px !important;
        margin-bottom: 5px !important;
    }
    
    /* Compact work done section */
    .work-done-section {
        margin-bottom: 15px !important;
        padding: 10px !important;
    }
    
    .work-done-section h3 {
        font-size: 12px !important;
        margin-bottom: 8px !important;
    }
    
    .work-description {
        font-size: 9px !important;
        line-height: 1.2 !important;
    }
    
    /* Compact table */
    .invoice-table-section {
        margin-bottom: 15px !important;
    }
    
    .invoice-table-section h3 {
        font-size: 12px !important;
        margin-bottom: 10px !important;
    }
    
    .invoice-table {
        font-size: 9px !important;
        margin-bottom: 10px !important;
        width: 100% !important;
        table-layout: fixed !important;
    }
    
    .invoice-table th {
        padding: 6px 4px !important;
        font-size: 8px !important;
        font-weight: 600 !important;
        border: 1px solid #333 !important;
        background: #f0f0f0 !important;
    }
    
    .invoice-table td {
        padding: 4px !important;
        border: 1px solid #ddd !important;
        vertical-align: top !important;
        font-size: 9px !important;
        line-height: 1.2 !important;
    }
    
    /* Table column widths for better fit */
    .invoice-table th.item-desc,
    .invoice-table td.item-desc {
        width: 50% !important;
    }
    
    .invoice-table th.qty, 
    .invoice-table th.unit-price, 
    .invoice-table th.total,
    .invoice-table td.qty, 
    .invoice-table td.unit-price, 
    .invoice-table td.total {
        width: 16% !important;
        text-align: right !important;
    }
    
    .item-row:nth-child(even) {
        background: #f9f9f9 !important;
    }
    
    .section-header td {
        background: #e9ecef !important;
        font-weight: bold !important;
        padding: 6px 4px !important;
        font-size: 9px !important;
    }
    
    .subtotal-row, .vat-row {
        background: #f8f9fa !important;
        font-weight: 600 !important;
    }
    
    .total-row {
        background: #007bff !important;
        color: white !important;
        font-weight: bold !important;
        font-size: 10px !important;
    }
    
    .totals-spacer {
        border: none !important;
        padding: 5px !important;
    }
    
    /* Compact payment information */
    .payment-info-section {
        margin-bottom: 15px !important;
        gap: 15px !important;
        font-size: 9px !important;
    }
    
    .banking-details, .payment-terms {
        padding: 10px !important;
    }
    
    .banking-details h3, .payment-terms h3 {
        font-size: 11px !important;
        margin-bottom: 8px !important;
    }
    
    .bank-info, .terms-content {
        font-size: 9px !important;
        line-height: 1.3 !important;
    }
    
    .bank-row {
        margin-bottom: 3px !important;
    }
    
    /* Compact footer */
    .invoice-footer {
        padding: 10px !important;
        margin-bottom: 10px !important;
        font-size: 9px !important;
    }
    
    /* Force single page */
    .invoice-container * {
        page-break-inside: avoid !important;
    }
    
    /* Prevent page breaks in critical sections */
    .invoice-header,
    .invoice-details-header,
    .work-done-section,
    .invoice-table-section,
    .payment-info-section,
    .invoice-footer {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    /* Ensure table rows don't break */
    .invoice-table tbody tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    /* Reduce spacing between sections */
    .invoice-header + .invoice-details-header,
    .invoice-details-header + .work-done-section,
    .work-done-section + .invoice-table-section,
    .invoice-table-section + .payment-info-section,
    .payment-info-section + .invoice-footer {
        margin-top: 0 !important;
    }
}

/* Specific A4 page settings */
@page {
    size: A4 portrait !important;
    margin: 8mm !important; /* Reduced margins */
    padding: 0 !important;
}

/* Additional compact print styles */
@media print {
    /* Hide any potential overflow */
    * {
        overflow: visible !important;
    }
    
    /* Ensure text fits */
    .text-sm {
        font-size: 8px !important;
    }
    
    .text-xs {
        font-size: 7px !important;
    }
    
    /* Compact badges and icons */
    .badge, .fa {
        font-size: 8px !important;
    }
    
    /* Reduce spacing in flex layouts */
    .flex {
        gap: 5px !important;
    }
    
    /* Compact list items */
    .bank-row, .client-info div {
        margin-bottom: 2px !important;
    }
    
    /* Ensure proper text wrapping */
    .item-desc {
        word-wrap: break-word !important;
        word-break: break-word !important;
    }
    
    /* Remove any box shadows or decorative elements */
    * {
        box-shadow: none !important;
        text-shadow: none !important;
        border-radius: 0 !important;
    }
    
    /* Optimize table for single page */
    .invoice-table {
        border-collapse: collapse !important;
        border-spacing: 0 !important;
    }
    
    /* Ensure critical content is visible */
    .total-row {
        font-size: 11px !important;
        font-weight: bold !important;
    }
    
    /* Compact multi-line content */
    br {
        line-height: 1.1 !important;
    }
}

/* Responsive Print Adjustments */
@media print and (max-height: 297mm) {
    .invoice-container {
        transform: scale(0.8) !important;
        height: 95vh !important;
    }
    
    .invoice-table {
        font-size: 8px !important;
    }
    
    .invoice-table th,
    .invoice-table td {
        padding: 3px 2px !important;
    }
}

@media print and (max-height: 280mm) {
    .invoice-container {
        transform: scale(0.75) !important;
        height: 90vh !important;
    }
    
    html, body {
        font-size: 9px !important;
    }
}

/* Force content to fit on one page */
@media print {
    .invoice-container {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        min-height: 100vh !important;
        max-height: 100vh !important;
    }
    
    .invoice-table-section {
        flex-grow: 1 !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    .invoice-table {
        flex-grow: 1 !important;
    }
}
</style>
@endsection