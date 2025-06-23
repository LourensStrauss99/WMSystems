@extends('layouts.app')  

@section('content')
<link href="{{ asset('/style.css') }}" rel="stylesheet">
<div class="container">
    <h2>Jobcard Search</h2>
    
    <!-- Enhanced Search Form -->
    <div class="search-section mb-4">
        <form method="GET" action="{{ route('jobcard.index') }}" class="search-form" id="searchForm">
            <div class="row">
                <!-- Client Search -->
                <div class="col-md-3 mb-2">
                    <label for="client" class="form-label">Client Name</label>
                    <input type="text" 
                           name="client" 
                           id="client"
                           placeholder="Search by client name" 
                           value="{{ request('client') }}" 
                           class="form-control" />
                </div>
                
                <!-- Jobcard Number Search -->
                <div class="col-md-3 mb-2">
                    <label for="jobcard_number" class="form-label">Jobcard Number</label>
                    <input type="text" 
                           name="jobcard_number" 
                           id="jobcard_number"
                           placeholder="e.g., JC-20250621-0001" 
                           value="{{ request('jobcard_number') }}" 
                           class="form-control" />
                </div>
                
                <!-- Date From -->
                <div class="col-md-2 mb-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           id="date_from"
                           value="{{ request('date_from') }}" 
                           class="form-control" />
                </div>
                
                <!-- Date To -->
                <div class="col-md-2 mb-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           id="date_to"
                           value="{{ request('date_to') }}" 
                           class="form-control" />
                </div>
                
                <!-- Status Filter -->
                <div class="col-md-2 mb-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <!-- Category Filter -->
                <div class="col-md-3 mb-2">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="assigned" {{ request('category') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in progress" {{ request('category') == 'in progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('category') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                
                <!-- Search Buttons -->
                <div class="col-md-9 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        🔍 Search
                    </button>
                    <button type="button" class="btn btn-secondary me-2" onclick="clearSearch()">
                        🗑️ Clear
                    </button>
                    <button type="button" class="btn btn-info" onclick="todayFilter()">
                        📅 Today
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Multi-level Dropdown Quick Filters -->
        <div class="quick-filters mt-3">
            <span class="me-2"><strong>Quick Filters:</strong></span>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i>Apply Filter
                </button>
                <ul class="dropdown-menu">
                    <!-- Status Section -->
                    <li class="dropdown-submenu">
                        <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-tasks me-2"></i>By Status
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="quickFilter('status', 'pending')">
                                <i class="fas fa-clock me-2 text-primary"></i>Pending
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="quickFilter('status', 'in_progress')">
                                <i class="fas fa-spinner me-2 text-warning"></i>In Progress
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="quickFilter('status', 'completed')">
                                <i class="fas fa-check-circle me-2 text-success"></i>Completed
                            </a></li>
                        </ul>
                    </li>
                    
                    <!-- Date Section -->
                    <li class="dropdown-submenu">
                        <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar me-2"></i>By Date
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="dateFilter('today')">
                                <i class="fas fa-calendar-day me-2 text-info"></i>Today
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="dateFilter('week')">
                                <i class="fas fa-calendar-week me-2 text-info"></i>This Week
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="dateFilter('month')">
                                <i class="fas fa-calendar-alt me-2 text-info"></i>This Month
                            </a></li>
                        </ul>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="clearAllFilters()">
                        <i class="fas fa-times-circle me-2"></i>Clear All Filters
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Search Results Summary -->
    @if(request()->hasAny(['client', 'jobcard_number', 'date_from', 'date_to', 'status', 'category']))
        <div class="search-summary mb-3">
            <div class="alert alert-info">
                <strong>Search Results:</strong>
                @if(request('client'))
                    Client: "{{ request('client') }}" |
                @endif
                @if(request('jobcard_number'))
                    Jobcard: "{{ request('jobcard_number') }}" |
                @endif
                @if(request('date_from') && request('date_to'))
                    Date Range: {{ request('date_from') }} to {{ request('date_to') }} |
                @elseif(request('date_from'))
                    From: {{ request('date_from') }} |
                @elseif(request('date_to'))
                    Until: {{ request('date_to') }} |
                @endif
                @if(request('status'))
                    Status: {{ ucfirst(request('status')) }} |
                @endif
                @if(request('category'))
                    Category: {{ ucfirst(request('category')) }}
                @endif
                
                @if($jobcards && $jobcards->total() > 0)
                    <span class="badge bg-success">{{ $jobcards->total() }} found</span>
                @else
                    <span class="badge bg-warning">No results found</span>
                @endif
            </div>
        </div>
    @endif

    @if($jobcards && $jobcards->count() > 0)
        <div class="table-wrapper" style="max-height: 600px; overflow-y: auto;">
            <table class="table" id="jobcardTable">
                <thead>
                    <tr>
                        <th>Jobcard #</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="jobcardBody">
                    @forelse($jobcards as $jobcard)
                        <tr
                            class="@if($jobcard->category == 'completed') tr-status-completed
                                   @elseif($jobcard->category == 'assigned') tr-status-assigned
                                   @elseif($jobcard->category == 'in progress') tr-status-in-progress
                                   @endif"
                            data-status="{{ $jobcard->status }}"
                            data-category="{{ $jobcard->category }}"
                        >
                            <td>{{ $jobcard->jobcard_number }}</td>
                            <td>{{ $jobcard->client->name }} {{ $jobcard->client->surname ?? '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($jobcard->job_date)->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge 
                                    @if($jobcard->status == 'completed') bg-success
                                    @elseif($jobcard->status == 'in_progress') bg-warning
                                    @elseif($jobcard->status == 'pending') bg-secondary
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $jobcard->status)) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($jobcard->category) }}</td>
                            <td>
                                <a href="{{ route('jobcard.show', $jobcard->id) }}"
                                   class="btn btn-sm btn-info view-edit-link"
                                >
                                    View/Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No jobcards found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($jobcards instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $jobcards->appends(request()->query())->links() }}
        @endif

        <!-- Loading indicator -->
        <div id="loading" style="display: none; text-align: center; padding: 20px;">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h4>No Jobcards Found</h4>
            <p>Try adjusting your search criteria or <a href="{{ route('jobcard.index') }}">view all jobcards</a>.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    const table = document.getElementById('jobcardTable');
    if (!table) {
        console.log('Table not found - probably no results');
        return;
    }
    console.log('Table found');

    const tbody = document.getElementById('jobcardBody');
    if (!tbody) {
        console.error('Tbody #jobcardBody not found');
        return;
    }
    console.log('Tbody found');

    // Direct event listeners for existing rows
    const rows = tbody.getElementsByTagName('tr');
    console.log('Number of rows:', rows.length);
    for (let row of rows) {
        row.addEventListener('click', function(e) {
            console.log('Row clicked:', this);
            handleRowClick(this, e);
        });
    }

    // Delegated event listener for dynamically added rows
    table.addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (row && row.parentElement.id === 'jobcardBody') {
            console.log('Delegated row click:', row);
            handleRowClick(row, e);
        }
    });

    // Ensure View/Edit links are not blocked
    document.querySelectorAll('.view-edit-link').forEach(link => {
        link.addEventListener('click', function(e) {
            console.log('View/Edit clicked, navigating to:', this.href);
            // No preventDefault here to allow navigation
        });
    });

    let page = 1;
    let isLoading = false;
    let nextPageUrl = @json($jobcards instanceof \Illuminate\Pagination\LengthAwarePaginator ? $jobcards->nextPageUrl() : null);
    console.log('Initial nextPageUrl:', nextPageUrl);

    function loadMoreJobcards() {
        if (isLoading || !nextPageUrl) {
            console.log('Not loading more: isLoading=', isLoading, 'nextPageUrl=', nextPageUrl);
            return;
        }

        isLoading = true;
        document.getElementById('loading').style.display = 'block';
        console.log('Fetching more jobcards from:', nextPageUrl);

        // Preserve search parameters in AJAX requests
        const url = new URL(nextPageUrl);
        const searchParams = new URLSearchParams(window.location.search);
        for (let [key, value] of searchParams) {
            url.searchParams.set(key, value);
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Fetch response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            const tbody = document.getElementById('jobcardBody');
            if (data.data.length === 0) {
                nextPageUrl = null;
                document.getElementById('loading').style.display = 'none';
                isLoading = false;
                console.log('No more jobcards to load');
                return;
            }

            data.data.forEach(jobcard => {
                const tr = document.createElement('tr');
                tr.setAttribute('data-status', jobcard.status);
                tr.setAttribute('data-category', jobcard.category);
                tr.classList.add(
                    jobcard.category === 'completed' ? 'tr-status-completed' :
                    jobcard.category === 'assigned' ? 'tr-status-assigned' :
                    jobcard.category === 'in progress' ? 'tr-status-in-progress' : ''
                );

                const statusBadgeClass = 
                    jobcard.status === 'completed' ? 'bg-success' :
                    jobcard.status === 'in_progress' ? 'bg-warning' :
                    jobcard.status === 'pending' ? 'bg-secondary' : 'bg-danger';

                tr.innerHTML = `
                    <td>${jobcard.jobcard_number}</td>
                    <td>${jobcard.client.name} ${jobcard.client.surname || ''}</td>
                    <td>${new Date(jobcard.job_date).toISOString().split('T')[0]}</td>
                    <td>
                        <span class="badge ${statusBadgeClass}">
                            ${jobcard.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                        </span>
                    </td>
                    <td>${jobcard.category.charAt(0).toUpperCase() + jobcard.category.slice(1)}</td>
                    <td>
                        <a href="/jobcard/${jobcard.id}"
                           class="btn btn-sm btn-info view-edit-link"
                        >
                            View/Edit
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            nextPageUrl = data.next_page_url;
            console.log('Updated nextPageUrl:', nextPageUrl);
            isLoading = false;
            document.getElementById('loading').style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading jobcards:', error);
            isLoading = false;
            document.getElementById('loading').style.display = 'none';
        });
    }

    window.addEventListener('scroll', function() {
        const triggerPoint = window.innerHeight + window.scrollY >= document.body.offsetHeight - 300;
        if (triggerPoint) {
            loadMoreJobcards();
        }
    });
});

function handleRowClick(row, e) {
    console.log('Handling row click:', row);
    // Only prevent default if targeting the row, not the link
    if (e.target.tagName !== 'A') {
        e.preventDefault();
        e.stopPropagation();
    }
    const status = row.getAttribute('data-status');
    const category = row.getAttribute('data-category');
    console.log('Status:', status, 'Category:', category);
    document.querySelectorAll('#jobcardTable tr.tr-highlight').forEach(function(tr) {
        tr.classList.remove('tr-highlight');
    });
    row.classList.add('tr-highlight');
    console.log('Class list:', row.classList);
}

// Search helper functions
function clearSearch() {
    document.getElementById('searchForm').reset();
    window.location.href = "{{ route('jobcard.index') }}";
}

function todayFilter() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_from').value = today;
    document.getElementById('date_to').value = today;
    document.getElementById('searchForm').submit();
}

function quickFilter(field, value) {
    event.preventDefault();
    const form = document.getElementById('searchForm');
    const input = document.getElementById(field);
    if (input) {
        input.value = value;
        form.submit();
    }
}

function dateFilter(period) {
    event.preventDefault();
    const today = new Date();
    let fromDate, toDate;
    
    switch(period) {
        case 'today':
            fromDate = toDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
            const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
            fromDate = firstDay.toISOString().split('T')[0];
            toDate = lastDay.toISOString().split('T')[0];
            break;
        case 'month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
    }
    
    document.getElementById('date_from').value = fromDate;
    document.getElementById('date_to').value = toDate;
    document.getElementById('searchForm').submit();
}

function clearAllFilters() {
    document.getElementById('searchForm').reset();
    window.location.href = "{{ route('jobcard.index') }}";
}

window.onload = function() {
    console.log('Window loaded');
};
</script>

<!-- Add some custom CSS for better styling -->
<style>
.search-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.quick-filters button {
    margin-bottom: 5px;
}

.search-summary {
    font-size: 0.9em;
}

.badge {
    font-size: 0.75em;
}

.tr-highlight {
    background-color: #e3f2fd !important;
}

.tr-status-completed {
    background-color: #f1f8e9;
}

.tr-status-assigned {
    background-color: #fff3e0;
}

.tr-status-in-progress {
    background-color: #e8f5e8;
}

/* Multi-level dropdown styles */
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu > .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -6px;
    margin-left: -1px;
    border-radius: 0 6px 6px 6px;
}

.dropdown-submenu:hover > .dropdown-menu {
    display: block;
}

.dropdown-submenu > a:after {
    display: block;
    content: " ";
    float: right;
    width: 0;
    height: 0;
    border-color: transparent;
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-left-color: #ccc;
    margin-top: 5px;
    margin-right: -10px;
}

.dropdown-submenu:hover > a:after {
    border-left-color: #fff;
}

.dropdown-submenu.pull-left {
    float: none;
}

.dropdown-submenu.pull-left > .dropdown-menu {
    left: -100%;
    margin-left: 10px;
    border-radius: 6px 0 6px 6px;
}

/* Quick filters styling */
.quick-filters .dropdown-menu {
    min-width: 200px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.quick-filters .dropdown-item {
    padding: 8px 16px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.quick-filters .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #495057;
    transform: translateX(4px);
}

.quick-filters .dropdown-item i {
    width: 16px;
    text-align: center;
}

.quick-filters .dropdown-toggle {
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.9rem;
    font-weight: 500;
}

.quick-filters .dropdown-toggle:hover {
    background-color: #f8f9fa;
    border-color: #6c757d;
}
</style>
@endsection