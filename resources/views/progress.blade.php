<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
   <!-- <style>
      /* Modal for job card view */
      .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        overflow: auto;
      }
      .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 800px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }
      .modal-content h2 {
        margin-top: 0;
      }
      .modal-content .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
      }
      .modal-content .close:hover,
      .modal-content .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
      }
      .modal-content .form-group {
        margin-bottom: 15px;
      }
      .modal-content label {
        display: block;
        font-weight: bold;
      }
      .modal-content input,
      .modal-content textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
      }
      .modal-content textarea {
        resize: vertical;
        min-height: 100px;
      }
      .modal-content table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
      }
      .modal-content th,
      .modal-content td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }
      .modal-content th {
        background-color: #f2f2f2;
      }
      /* Highlight selected row */
      #assigned-jobcards-table-body tr.selected {
        background-color: #d1e7ff;
        font-weight: bold;
      }
      #assigned-jobcards-table-body tr:hover {
        background-color: #f0f0f0;
        cursor: pointer;
      }
      /* Scrollable table */
      .assigned-jobcards {
        max-height: 400px;
        overflow-y: auto;
      }
      .assigned-jobcards table {
        width: 100%;
      }
      /* Date range search */
      .date-search {
        margin-top: 10px;
        display: flex;
        gap: 10px;
        align-items: center;
      }
      .date-search label {
        font-weight: bold;
      }
      .date-search input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
      }
      @media print {
        .modal {
          background-color: transparent;
        }
        .modal-content {
          margin: 0;
          width: 100%;
          box-shadow: none;
          border: none;
        }
        .no-print {
          display: none;
        }
      }
    </style>  -->
  </head>
  <body>
<div class="container">  
  <!-- Header -->
  

  {{-- filepath: resources/views/progress.blade.php --}}
@extends('layouts.app')
@extends('layouts.nav')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Jobcards Overview</h2>
    <div class="row">
        <!-- Assigned Column -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <strong>Assigned</strong>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="assigned-list">
                    <ul class="list-group" id="assigned-jobcards-list">
                        @foreach($assignedJobcards as $jobcard)
                            <li class="list-group-item d-flex justify-content-between align-items-center jobcard-row"
                                ondblclick="window.location='{{ route('progress.jobcard.show', $jobcard->id) }}'">
                                <div>
                                    <strong>#{{ $jobcard->jobcard_number }}</strong><br>
                                    {{ $jobcard->client->name ?? '' }}<br>
                                    <small>{{ $jobcard->job_date }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- In Progress Column -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header" style="background-color: #f75461; color: #721c24;">
                    <strong>In Progress</strong>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="inprogress-list">
                    <ul class="list-group" id="inprogress-jobcards-list">
                        @foreach($inProgressJobcards as $jobcard)
                            <li class="list-group-item d-flex justify-content-between align-items-center jobcard-row"
                                ondblclick="window.location='{{ route('progress.jobcard.show', $jobcard->id) }}'">
                                <div>
                                    <strong>#{{ $jobcard->jobcard_number }}</strong><br>
                                    {{ $jobcard->client->name ?? '' }}<br>
                                    <small>{{ $jobcard->job_date }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- Completed Column -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header" style="background-color: green; color: white;">
                    <strong>Completed</strong>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="completed-list">
                    <ul class="list-group" id="completed-jobcards-list">
                        @foreach($completedJobcards as $jobcard)
                            <li class="list-group-item d-flex justify-content-between align-items-center jobcard-row"
                                ondblclick="window.location='{{ route('progress.jobcard.show', $jobcard->id) }}'">
                                <div>
                                    <strong>#{{ $jobcard->jobcard_number }}</strong><br>
                                    {{ $jobcard->client->name ?? '' }}<br>
                                    <small>{{ $jobcard->job_date }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('progress') }}" class="btn btn-secondary mb-3">Back to Progress</a>
</div>
@endsection

  <script>
let currentJobcardId = null;
let sparesData = [];

function viewJobcard(id) {
    fetch('/progress/jobcard/' + id)
        .then(response => response.json())
        .then data => {
            currentJobcardId = id;
            document.getElementById('modal-jobcard-id').value = id;
            document.getElementById('view-jobcard-number').value = data.jobcard_number || '';
            document.getElementById('view-job-date').value = data.job_date || '';
            document.getElementById('view-client-name').value = data.client?.name || '';
            document.getElementById('view-client-telephone').value = data.client?.telephone || '';
            document.getElementById('view-client-address').value = data.client?.address || '';
            document.getElementById('view-client-email').value = data.client?.email || '';
            document.getElementById('view-category').value = data.category || '';
            document.getElementById('view-artisan').value = (data.employees && data.employees.length) ? data.employees.map(e => e.name).join(', ') : '';
            document.getElementById('view-work-request').value = data.work_request || '';
            document.getElementById('edit-work-done').value = data.work_done || '';
            document.getElementById('edit-hours').value = data.time_spent || '';
            document.getElementById('view-special-request').value = data.special_request || '';
            document.getElementById('progress-note').value = data.progress_note || '';

            // Clear and populate spares table
            const sparesTableBody = document.getElementById('edit-spares-table-body');
            sparesTableBody.innerHTML = '';
            data.spares.forEach(spare => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${spare.name}</td>
                    <td>${spare.part_number}</td>
                    <td>
                        <input type="number" name="spares[${spare.id}]" class="form-control" min="0" value="${spare.pivot.quantity}">
                    </td>
                `;
                sparesTableBody.appendChild(row);
            });

            // Show modal
            document.getElementById('jobcard-modal').style.display = 'block';
        })
        .catch(error => console.error('Error fetching jobcard data:', error));
}

function closeJobcard() {
    document.getElementById('jobcard-modal').style.display = 'none';
    currentJobcardId = null;
}

function markCompleted() {
    if (currentJobcardId) {
        fetch('/progress/jobcard/' + currentJobcardId + '/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ completed: true })
        })
        .then(response => {
            if (response.ok) {
                alert('Jobcard marked as completed.');
                location.reload();
            } else {
                alert('Error marking jobcard as completed.');
            }
        })
        .catch(error => console.error('Error marking jobcard as completed:', error));
    }
}

function submitForInvoice() {
    if (currentJobcardId) {
        fetch('/progress/jobcard/' + currentJobcardId + '/submit-invoice', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ submitted: true })
        })
        .then(response => {
            if (response.ok) {
                alert('Jobcard submitted for invoice.');
                location.reload();
            } else {
                alert('Error submitting jobcard for invoice.');
            }
        })
        .catch(error => console.error('Error submitting jobcard for invoice:', error));
    }
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('jobcard-modal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setupInfiniteScroll(listId, url, pageParam) {
    let page = 1;
    let loading = false;
    const list = document.getElementById(listId);

    list.addEventListener('scroll', function() {
        if (loading) return;
        if (list.scrollTop + list.clientHeight >= list.scrollHeight - 10) {
            loading = true;
            page++;
            fetch(url + '?' + pageParam + '=' + page, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    if (html.trim() !== '') {
                        list.querySelector('ul').insertAdjacentHTML('beforeend', html);
                        loading = false;
                    }
                });
        }
    });
}

setupInfiniteScroll('assigned-list', '/progress/assigned', 'assigned_page');
setupInfiniteScroll('inprogress-list', '/progress/inprogress', 'inprogress_page');
setupInfiniteScroll('completed-list', '/progress/completed', 'completed_page');
  </script>
  </body>
</html>