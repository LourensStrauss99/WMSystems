@extends('layouts.app')
@extends('layouts.nav')
@section('content')
<link href="{{ asset('/style.css') }}" rel="stylesheet">
<div class="container">
    <h2>Jobcard Search</h2>
    <form method="GET" action="{{ route('jobcard.index') }}" class="mb-3" id="searchForm">
        <input type="text" name="client" placeholder="Search by client name" value="{{ request('client') }}" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    

    @if($jobcards)
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
                            <td>{{ $jobcard->client->name }}</td>
                            <td>{{ $jobcard->job_date }}</td>
                            <td>{{ $jobcard->status }}</td>
                            <td>{{ $jobcard->category }}</td>
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
        <div id="loading" class="text-center" style="display: none;">
            Loading more jobcards...
        </div>
    @else
        <p>No jobcards available. Please check your search.</p>
    @endif
</div>

<script>
console.log('Script loaded at the top');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    console.log('Window object:', window);

    const table = document.getElementById('jobcardTable');
    if (!table) {
        console.error('Table #jobcardTable not found');
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

        fetch(nextPageUrl, {
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

                tr.innerHTML = `
                    <td>${jobcard.jobcard_number}</td>
                    <td>${jobcard.client.name}</td>
                    <td>${jobcard.job_date}</td>
                    <td>${jobcard.status}</td>
                    <td>${jobcard.category}</td>
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
        console.log('Scroll event - Trigger point reached:', triggerPoint, 'Window height:', window.innerHeight, 'Scroll Y:', window.scrollY, 'Body height:', document.body.offsetHeight);
        if (triggerPoint) {
            loadMoreJobcards();
        }
    });

    if (nextPageUrl) {
        console.log('Adding scroll listener for infinite scroll');
        window.addEventListener('scroll', loadMoreJobcards);
    }
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

window.onload = function() {
    console.log('Window loaded');
};
</script>
@endsection