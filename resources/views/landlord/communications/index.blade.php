@extends('layouts.app')

@section('title', 'Tenant Communications')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Tenant Communications</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#newCommunicationModal">
                        <i class="fas fa-plus"></i> New Communication
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Open Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $communications->where('status', 'open')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $communications->where('status', 'resolved')->where('resolved_at', '>=', today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                High Priority
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $communications->where('priority', 'high')->whereIn('status', ['open', 'pending'])->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Response
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $avgResponse = $communications->whereNotNull('first_response_at')->avg(function($comm) {
                                        return $comm->created_at->diffInMinutes($comm->first_response_at);
                                    });
                                @endphp
                                {{ $avgResponse ? round($avgResponse) . ' min' : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Communications Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-0 font-weight-bold text-primary">Communications ({{ $communications->total() }} total)</h6>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary" onclick="filterCommunications('all')">All</button>
                                <button class="btn btn-outline-primary" onclick="filterCommunications('open')">Open</button>
                                <button class="btn btn-outline-warning" onclick="filterCommunications('high')">High Priority</button>
                                <button class="btn btn-outline-success" onclick="filterCommunications('resolved')">Resolved</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($communications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Tenant</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Initiated</th>
                                        <th>Last Activity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($communications as $communication)
                                        <tr class="communication-row" 
                                            data-status="{{ $communication->status }}" 
                                            data-priority="{{ $communication->priority }}"
                                            data-category="{{ $communication->category }}">
                                            <td>
                                                <div>
                                                    <strong>{{ $communication->subject }}</strong>
                                                    @if($communication->messages->count() > 0)
                                                        <br><small class="text-muted">{{ $communication->messages->count() }} message{{ $communication->messages->count() != 1 ? 's' : '' }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $communication->tenant->name ?? 'Unknown' }}
                                                    <br><small class="text-muted">{{ $communication->initiatedBy->name ?? 'System' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $communication->category === 'support' ? 'info' : ($communication->category === 'billing' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($communication->category) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $communication->priority === 'high' ? 'danger' : ($communication->priority === 'medium' ? 'warning' : 'success') }}">
                                                    {{ ucfirst($communication->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $communication->status === 'resolved' ? 'success' : ($communication->status === 'open' ? 'primary' : 'secondary') }}">
                                                    {{ ucfirst($communication->status) }}
                                                </span>
                                                @if($communication->response_time && $communication->status !== 'resolved')
                                                    <br><small class="text-muted">Response: {{ $communication->response_time }}min</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $communication->created_at->format('M d, Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $communication->last_activity_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="View Conversation"
                                                            onclick="viewCommunication({{ $communication->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success" title="Reply"
                                                            onclick="replyCommunication({{ $communication->id }})">
                                                        <i class="fas fa-reply"></i>
                                                    </button>
                                                    @if($communication->status === 'open')
                                                        <button class="btn btn-outline-warning" title="Mark as Resolved"
                                                                onclick="resolveCommunication({{ $communication->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#" onclick="assignCommunication({{ $communication->id }})">
                                                                <i class="fas fa-user"></i> Assign To
                                                            </a>
                                                            <a class="dropdown-item" href="#" onclick="changePriority({{ $communication->id }})">
                                                                <i class="fas fa-flag"></i> Change Priority
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            @if($communication->status === 'resolved')
                                                                <a class="dropdown-item" href="#" onclick="reopenCommunication({{ $communication->id }})">
                                                                    <i class="fas fa-undo"></i> Reopen
                                                                </a>
                                                            @else
                                                                <a class="dropdown-item" href="#" onclick="closeCommunication({{ $communication->id }})">
                                                                    <i class="fas fa-times"></i> Close
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $communications->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No communications found.</p>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#newCommunicationModal">
                                <i class="fas fa-plus"></i> Start First Communication
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Communication Modal -->
<div class="modal fade" id="newCommunicationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Communication</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="newCommunicationForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tenant_id">Tenant <span class="text-danger">*</span></label>
                                <select class="form-control" id="tenant_id" name="tenant_id" required>
                                    <option value="">Select Tenant</option>
                                    <!-- Tenant options would be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing Inquiry</option>
                                    <option value="general">General Question</option>
                                    <option value="complaint">Complaint</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority">Priority <span class="text-danger">*</span></label>
                                <select class="form-control" id="priority" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tags">Tags</label>
                                <input type="text" class="form-control" id="tags" name="tags" 
                                       placeholder="e.g., urgent, billing, technical">
                                <small class="form-text text-muted">Separate multiple tags with commas</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Initial Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Start Communication
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterCommunications(filter) {
    const rows = document.querySelectorAll('.communication-row');
    
    rows.forEach(row => {
        let show = false;
        
        switch(filter) {
            case 'all':
                show = true;
                break;
            case 'open':
                show = row.dataset.status === 'open';
                break;
            case 'high':
                show = row.dataset.priority === 'high';
                break;
            case 'resolved':
                show = row.dataset.status === 'resolved';
                break;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function viewCommunication(communicationId) {
    // Implement view communication details
    alert('View communication details for ID: ' + communicationId);
}

function replyCommunication(communicationId) {
    // Implement reply functionality
    alert('Reply to communication ID: ' + communicationId);
}

function resolveCommunication(communicationId) {
    if (confirm('Mark this communication as resolved?')) {
        // Implement resolve functionality
        alert('Resolve communication ID: ' + communicationId);
    }
}

function assignCommunication(communicationId) {
    // Implement assign functionality
    alert('Assign communication ID: ' + communicationId);
}

function changePriority(communicationId) {
    // Implement change priority functionality
    alert('Change priority for communication ID: ' + communicationId);
}

function reopenCommunication(communicationId) {
    if (confirm('Reopen this communication?')) {
        // Implement reopen functionality
        alert('Reopen communication ID: ' + communicationId);
    }
}

function closeCommunication(communicationId) {
    if (confirm('Close this communication? It will no longer be active.')) {
        // Implement close functionality
        alert('Close communication ID: ' + communicationId);
    }
}

// Handle new communication form submission
document.getElementById('newCommunicationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = new FormData(this);
    
    // Here you would typically send an AJAX request to create the communication
    alert('Create communication functionality would be implemented here');
    
    // Close modal after successful creation
    $('#newCommunicationModal').modal('hide');
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endsection
