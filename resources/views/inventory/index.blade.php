@extends('layouts.app')
@extends('layouts.nav')
@section('content')

<div class="bg-white p-6 rounded shadow" style="width:95%; margin:auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-xl font-bold text-center">Inventory Management</h1>
        <button class="btn btn-primary" onclick="refreshStock()">🔄 Refresh Stock</button>
    </div>

    <!-- Enhanced Clickable Stock Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center stock-card {{ request('filter') == 'all' || !request('filter') ? 'active-filter' : '' }}" 
                 onclick="filterStock('all')" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">📦 Total Items</h5>
                    <h2 class="text-primary">{{ $stats['total_items'] }}</h2>
                    <small class="text-muted">Click to view all</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stock-card {{ request('filter') == 'low_stock' ? 'active-filter' : '' }}" 
                 onclick="filterStock('low_stock')" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">⚠️ Low Stock</h5>
                    <h2 class="text-warning">{{ $stats['low_stock_items'] }}</h2>
                    <small class="text-muted">Click to view low stock</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stock-card {{ request('filter') == 'critical' ? 'active-filter' : '' }}" 
                 onclick="filterStock('critical')" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">🚨 Critical</h5>
                    <h2 class="text-danger">{{ $stats['critical_items'] }}</h2>
                    <small class="text-muted">Click to view critical</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center stock-card {{ request('filter') == 'out_of_stock' ? 'active-filter' : '' }}" 
                 onclick="filterStock('out_of_stock')" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">❌ Out of Stock</h5>
                    <h2 class="text-dark">{{ $stats['out_of_stock'] }}</h2>
                    <small class="text-muted">Click to view out of stock</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Filter Display -->
    @if(request('filter'))
        <div class="alert alert-info mb-3" id="current-filter">
            <div class="d-flex justify-content-between align-items-center">
                <span>
                    <strong>📋 Current Filter:</strong> 
                    @switch(request('filter'))
                        @case('low_stock')
                            <span class="badge bg-warning">⚠️ Low Stock Items</span> - Items at or below minimum level
                            @break
                        @case('critical')
                            <span class="badge bg-danger">🚨 Critical Items</span> - Items below 50% of minimum level
                            @break
                        @case('out_of_stock')
                            <span class="badge bg-dark">❌ Out of Stock</span> - Items with zero stock
                            @break
                        @default
                            <span class="badge bg-primary">📦 All Items</span>
                    @endswitch
                </span>
                <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                    ✖️ Clear Filter
                </button>
            </div>
        </div>
    @endif

    <!-- Search and Filter Form -->
    <form method="GET" action="/inventory" class="mb-4" id="search-form">
        <input type="hidden" name="filter" id="filter-input" value="{{ request('filter') }}">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="search" placeholder="Search by name, code, or description..." 
                       value="{{ request('search') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <select name="stock_filter" class="form-control">
                    <option value="">All Stock Levels</option>
                    <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="critical" {{ request('stock_filter') == 'critical' ? 'selected' : '' }}>Critical Stock</option>
                    <option value="out_of_stock" {{ request('stock_filter') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">🔍 Search</button>
                <a href="/inventory" class="btn btn-secondary">Clear All</a>
            </div>
        </div>
    </form>

    <!-- Results Count -->
    <div class="mb-3">
        <span class="text-muted">
            <strong>{{ $items->count() }}</strong> items found
            @if(request('filter') || request('search') || request('stock_filter'))
                (filtered)
            @endif
        </span>
    </div>

    <!-- Inventory Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover" id="inventoryTable">
            <thead class="table-dark">
                <tr>
                    <th>Status</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Supplier</th>
                    <th>Stock Level</th>
                    <th>Min Level</th>
                    <th>Selling Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                @php $stockStatus = $item->getStockStatus(); @endphp
                <tr onclick="highlightRow(this)" 
                    class="inventory-row {{ $item->isAtMinLevel() ? 'table-warning' : '' }} {{ $item->isCriticallyLow() ? 'table-danger' : '' }}"
                    data-item-id="{{ $item->id }}">
                    
                    <td>
                        <span class="badge {{ $stockStatus['class'] }}">
                            {{ $stockStatus['icon'] }} {{ $stockStatus['status'] }}
                        </span>
                    </td>
                    
                    <td><strong>{{ $item->short_code }}</strong></td>
                    
                    <td>{{ $item->name }}</td>
                    
                    <td>{{ $item->short_description }}</td>
                    
                    <td>{{ $item->supplier }}</td>
                    
                    <td>
                        <span class="stock-level {{ $item->isAtMinLevel() ? 'text-danger' : 'text-success' }}">
                            {{ $item->stock_level }}
                        </span>
                        @if($item->isAtMinLevel())
                            <small class="text-danger d-block">⚠️ Below Min Level</small>
                        @endif
                    </td>
                    
                    <td>{{ $item->min_level }}</td>
                    
                    <td>R{{ number_format($item->selling_price, 2) }}</td>
                    
                    <td>
                        <button class="btn btn-sm btn-info" onclick="viewItem({{ $item->id }})">
                            👁️ View
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editStock({{ $item->id }})">
                            ✏️ Edit
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="alert alert-info mb-0">
                            <h5>No inventory items found</h5>
                            <p class="mb-0">
                                @if(request('filter') || request('search') || request('stock_filter'))
                                    No items match your current filter criteria. 
                                    <button class="btn btn-sm btn-outline-primary" onclick="clearFilters()">Clear Filters</button>
                                @else
                                    Start by adding some inventory items.
                                    <a href="/master-settings" class="btn btn-sm btn-outline-primary">Add Items</a>
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
// Filter stock by category
function filterStock(category) {
    const url = new URL(window.location.href);
    
    // Clear existing filters
    url.searchParams.delete('stock_filter');
    url.searchParams.delete('search');
    
    // Set new filter
    if (category === 'all') {
        url.searchParams.delete('filter');
    } else {
        url.searchParams.set('filter', category);
    }
    
    // Navigate to filtered URL
    window.location.href = url.toString();
}

// Clear all filters
function clearFilters() {
    window.location.href = '/inventory';
}

function highlightRow(row) {
    // Remove highlight from all rows
    document.querySelectorAll('tr.tr-highlight').forEach(function(tr) {
        tr.classList.remove('tr-highlight');
    });
    // Add highlight to the clicked row
    row.classList.add('tr-highlight');
}

function refreshStock() {
    window.location.reload();
}

function viewItem(itemId) {
    // You can implement a detailed view modal here
    console.log('View item:', itemId);
}

function editStock(itemId) {
    window.location.href = `/inventory/${itemId}/edit`;
}

// Auto-refresh stock levels every 30 seconds
setInterval(function() {
    updateStockLevels();
}, 30000);

function updateStockLevels() {
    fetch('/inventory/stock-alerts')
        .then(response => response.json())
        .then(data => {
            // Update stock level indicators
            data.forEach(item => {
                const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
                if (row) {
                    const stockCell = row.querySelector('.stock-level');
                    if (stockCell) {
                        stockCell.textContent = item.current_stock;
                        stockCell.className = `stock-level ${item.current_stock <= item.min_level ? 'text-danger' : 'text-success'}`;
                    }
                }
            });
        })
        .catch(error => console.error('Error updating stock levels:', error));
}

// Add hover effects and loading states
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state when clicking filter cards
    document.querySelectorAll('.stock-card').forEach(card => {
        card.addEventListener('click', function() {
            // Add loading state
            this.style.opacity = '0.6';
            this.innerHTML += '<div class="text-center mt-2"><small>Loading...</small></div>';
        });
    });
});
</script>

<style>
.tr-highlight {
    background-color: #e3f2fd !important;
}

.inventory-row:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.table-warning {
    background-color: #fff3cd !important;
}

.table-danger {
    background-color: #f8d7da !important;
}

.stock-level {
    font-weight: bold;
    font-size: 1.1em;
}

.badge {
    font-size: 0.8em;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/* Enhanced card styling for clickable stock cards */
.stock-card {
    position: relative;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.stock-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.stock-card.active-filter {
    border-color: #007bff;
    background-color: #f8f9ff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.stock-card .card-body {
    padding: 1.5rem;
}

.stock-card small {
    opacity: 0.8;
    font-size: 0.75rem;
}

/* Pulse animation for active filter */
.active-filter {
    animation: pulse-border 2s infinite;
}

@keyframes pulse-border {
    0% { border-color: #007bff; }
    50% { border-color: #66b3ff; }
    100% { border-color: #007bff; }
}

/* Loading state */
.stock-card.loading {
    opacity: 0.6;
    pointer-events: none;
}
</style>
@endsection