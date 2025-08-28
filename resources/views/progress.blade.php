
@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📋 Progress</h2>
        <div class="board-stats">
            <span class="stat-pill assigned">{{ count($assignedJobcards) }} Assigned</span>
            <span class="stat-pill in-progress">{{ count($inProgressJobcards) }} In Progress</span>
            <span class="stat-pill completed">{{ count($completedJobcards) }} Completed</span>
        </div>
    </div>

    <!-- Modern Kanban Columns -->
    <div class="row progress-board">
        
        <!-- Assigned Column -->
        <div class="col-md-4">
            <div class="progress-column assigned-column">
                <div class="column-header">
                    <div class="header-content">
                        <h5>📋 Assigned</h5>
                        <span class="count-badge">{{ count($assignedJobcards) }}</span>
                    </div>
                    <div class="header-actions">
                        <button class="column-btn" title="Add New">➕</button>
                        <button class="column-btn" title="Filter">🔍</button>
                    </div>
                </div>
                
                <div class="column-body">
                    @forelse($assignedJobcards as $jobcard)
                        <div class="jobcard-card assigned" onclick="window.location.href='{{ route('progress.jobcard.show', $jobcard->id) }}'">
                            <div class="card-header">
                                <span class="jobcard-id">#{{ $jobcard->jobcard_number }}</span>
                                <span class="status-dot assigned"></span>
                            </div>
                            
                            <div class="card-content">
                                <h6 class="client-name">{{ Str::limit($jobcard->client->name ?? 'No Client', 25) }}</h6>
                                <p class="work-request">{{ Str::limit($jobcard->work_request ?? 'No details', 60) }}</p>
                            </div>
                            
                            <div class="card-meta">
                                <div class="meta-item">
                                    <span class="meta-icon">📅</span>
                                    <span class="meta-text">{{ \Carbon\Carbon::parse($jobcard->job_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-icon">🏷️</span>
                                    <span class="meta-text">{{ $jobcard->category ?? 'General' }}</span>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <span class="time-ago">{{ \Carbon\Carbon::parse($jobcard->created_at)->diffForHumans() }}</span>
                                <div class="progress-indicator">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 10%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="empty-icon">📝</span>
                            <p>No assigned jobcards</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="col-md-4">
            <div class="progress-column in-progress-column">
                <div class="column-header">
                    <div class="header-content">
                        <h5>⚡ In Progress</h5>
                        <span class="count-badge">{{ count($inProgressJobcards) }}</span>
                    </div>
                    <div class="header-actions">
                        <button class="column-btn" title="View All">👁️</button>
                        <button class="column-btn" title="Filter">🔍</button>
                    </div>
                </div>
                
                <div class="column-body">
                    @forelse($inProgressJobcards as $jobcard)
                        <div class="jobcard-card in-progress" onclick="window.location.href='{{ route('progress.jobcard.show', $jobcard->id) }}'">
                            <div class="card-header">
                                <span class="jobcard-id">#{{ $jobcard->jobcard_number }}</span>
                                <span class="status-dot in-progress"></span>
                            </div>
                            
                            <div class="card-content">
                                <h6 class="client-name">{{ Str::limit($jobcard->client->name ?? 'No Client', 25) }}</h6>
                                <p class="work-request">{{ Str::limit($jobcard->work_request ?? 'No details', 60) }}</p>
                            </div>
                            
                            <div class="card-meta">
                                <div class="meta-item">
                                    <span class="meta-icon">👨‍🔧</span>
                                    <span class="meta-text">
                                        @if($jobcard->employees && $jobcard->employees->count())
                                            {{ Str::limit($jobcard->employees->first()->name, 15) }}
                                            @if($jobcard->employees->count() > 1)
                                                +{{ $jobcard->employees->count() - 1 }}
                                            @endif
                                        @else
                                            Unassigned
                                        @endif
                                    </span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-icon">⏱️</span>
                                    <span class="meta-text">
                                        @if($jobcard->employees && $jobcard->employees->count())
                                            {{ $jobcard->employees->sum('pivot.hours_worked') ?? 0 }}h
                                        @else
                                            0h
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <span class="time-ago">{{ \Carbon\Carbon::parse($jobcard->updated_at)->diffForHumans() }}</span>
                                <div class="progress-indicator">
                                    <div class="progress-bar">
                                        <div class="progress-fill in-progress" style="width: 65%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="empty-icon">⚡</span>
                            <p>No jobs in progress</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="col-md-4">
            <div class="progress-column completed-column">
                <div class="column-header">
                    <div class="header-content">
                        <h5>✅ Completed</h5>
                        <span class="count-badge">{{ count($completedJobcards) }}</span>
                    </div>
                    <div class="header-actions">
                        <button class="column-btn" title="Create Invoice">💰</button>
                        <button class="column-btn" title="Filter">🔍</button>
                    </div>
                </div>
                
                <div class="column-body">
                    @forelse($completedJobcards as $jobcard)
                        <div class="jobcard-card completed" onclick="window.location.href='{{ route('progress.jobcard.show', $jobcard->id) }}'">
                            <div class="card-header">
                                <span class="jobcard-id">#{{ $jobcard->jobcard_number }}</span>
                                <span class="status-dot completed"></span>
                            </div>
                            
                            <div class="card-content">
                                <h6 class="client-name">{{ Str::limit($jobcard->client->name ?? 'No Client', 25) }}</h6>
                                <p class="work-request">{{ Str::limit($jobcard->work_request ?? 'No details', 60) }}</p>
                            </div>
                            
                            <div class="card-meta">
                                <div class="meta-item">
                                    <span class="meta-icon">✅</span>
                                    <span class="meta-text">{{ \Carbon\Carbon::parse($jobcard->updated_at)->format('M d') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-icon">⏱️</span>
                                    <span class="meta-text">
                                        @if($jobcard->employees && $jobcard->employees->count())
                                            {{ $jobcard->employees->sum('pivot.hours_worked') ?? 0 }}h total
                                        @else
                                            0h total
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <span class="invoice-status">Ready for Invoice</span>
                                <div class="progress-indicator">
                                    <div class="progress-bar">
                                        <div class="progress-fill completed" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="empty-icon">✅</span>
                            <p>No completed jobs</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Force horizontal layout */
.progress-board {
    display: flex !important;
    flex-direction: row !important;
    min-height: 60vh;
    gap: 8px;
    margin: 0 -5px;
}

.progress-board .col-md-4 {
    flex: 1 !important;
    max-width: none !important;
    width: 33.333% !important;
    padding: 0 5px !important;
    margin-bottom: 0 !important;
}

/* Ensure containers fit properly */
.container-fluid {
    padding-left: 10px;
    padding-right: 10px;
}

/* Make columns same height */
.progress-column {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 14px rgba(0,0,0,0.07);
    height: 64vh;
    display: flex;
    flex-direction: column;
    border: 1px solid #e9ecef;
    overflow: hidden;
    width: 100%;
}

/* Main Board Styling */
.progress-board {
    min-height: 60vh; /* Reduced from 70vh */
    gap: 10px; /* Reduced from 15px */
}

.board-stats {
    display: flex;
    gap: 10px; /* Reduced from 12px */
    flex-wrap: wrap;
}

.stat-pill {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 5px 10px; /* Reduced from 6px 12px */
    border-radius: 16px; /* Reduced from 20px */
    font-size: 0.72em; /* Reduced from 0.85em */
    font-weight: 600;
    transition: all 0.2s ease;
}

.stat-pill.assigned { border-color: #6c757d; color: #6c757d; }
.stat-pill.in-progress { border-color: #fd7e14; color: #fd7e14; }
.stat-pill.completed { border-color: #28a745; color: #28a745; }

.stat-pill:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.column-header {
    padding: 12px 16px; /* Reduced from 16px 20px */
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
}

.assigned-column .column-header { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
.in-progress-column .column-header { background: linear-gradient(135deg, #fff3e0 0%, #ffecb3 100%); }
.completed-column .column-header { background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); }

.header-content {
    display: flex;
    align-items: center;
    gap: 8px; /* Reduced from 10px */
}

.header-content h5 {
    margin: 0;
    font-size: 0.95em; /* Reduced from 1.1em */
    font-weight: 600;
    color: #495057;
}

.count-badge {
    background: #007bff;
    color: white;
    padding: 3px 7px; /* Reduced from 4px 8px */
    border-radius: 10px; /* Reduced from 12px */
    font-size: 0.68em; /* Reduced from 0.75em */
    font-weight: 700;
    min-width: 20px; /* Reduced from 24px */
    text-align: center;
}

.assigned-column .count-badge { background: #6c757d; }
.in-progress-column .count-badge { background: #fd7e14; }
.completed-column .count-badge { background: #28a745; }

.header-actions {
    display: flex;
    gap: 5px; /* Reduced from 6px */
}

.column-btn {
    background: #fff;
    border: 1px solid #dee2e6;
    padding: 5px 7px; /* Reduced from 6px 8px */
    border-radius: 5px; /* Reduced from 6px */
    cursor: pointer;
    font-size: 0.7em; /* Reduced from 0.8em */
    transition: all 0.2s ease;
}

.column-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
    transform: translateY(-1px);
}

.column-body {
    flex: 1;
    padding: 12px; /* Reduced from 15px */
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px; /* Reduced from 12px */
}

/* Jobcard Cards - Reduced sizes */
.jobcard-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 7px; /* Reduced from 8px */
    padding: 12px; /* Reduced from 16px */
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.jobcard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 18px rgba(0,0,0,0.1); /* Slightly reduced */
    border-color: #007bff;
}

.jobcard-card.assigned { border-left: 3px solid #6c757d; } /* Reduced from 4px */
.jobcard-card.in-progress { border-left: 3px solid #fd7e14; }
.jobcard-card.completed { border-left: 3px solid #28a745; }

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px; /* Reduced from 12px */
}

.jobcard-id {
    background: #e3f0fb;
    color: #007bff;
    padding: 3px 6px; /* Reduced from 4px 8px */
    border-radius: 4px;
    font-size: 0.7em; /* Reduced from 0.8em */
    font-weight: 700;
    font-family: 'Courier New', monospace;
}

.status-dot {
    width: 8px; /* Reduced from 10px */
    height: 8px;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.status-dot.assigned { background: #6c757d; }
.status-dot.in-progress { background: #fd7e14; }
.status-dot.completed { background: #28a745; }

.card-content {
    margin-bottom: 10px; /* Reduced from 12px */
}

.client-name {
    font-size: 0.9em; /* Reduced from 1em */
    font-weight: 600;
    color: #212529;
    margin-bottom: 5px; /* Reduced from 6px */
}

.work-request {
    font-size: 0.75em; /* Reduced from 0.85em */
    color: #6c757d;
    line-height: 1.3; /* Reduced from 1.4 */
    margin: 0;
}

.card-meta {
    margin-bottom: 10px; /* Reduced from 12px */
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px; /* Reduced from 6px */
    margin-bottom: 3px; /* Reduced from 4px */
    font-size: 0.7em; /* Reduced from 0.8em */
}

.meta-icon {
    font-size: 0.68em; /* Reduced from 0.75em */
    width: 14px; /* Reduced from 16px */
}

.meta-text {
    color: #495057;
    font-weight: 500;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.time-ago {
    font-size: 0.68em; /* Reduced from 0.75em */
    color: #6c757d;
    font-style: italic;
}

.invoice-status {
    font-size: 0.68em; /* Reduced from 0.75em */
    color: #28a745;
    font-weight: 600;
}

.progress-indicator {
    flex: 1;
    max-width: 50px; /* Reduced from 60px */
    margin-left: 8px; /* Reduced from 10px */
}

.progress-bar {
    width: 100%;
    height: 3px; /* Reduced from 4px */
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #007bff;
    transition: width 0.3s ease;
    border-radius: 2px;
}

.progress-fill.assigned { background: #6c757d; }
.progress-fill.in-progress { background: #fd7e14; }
.progress-fill.completed { background: #28a745; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 30px 15px; /* Reduced from 40px 20px */
    color: #6c757d;
}

.empty-icon {
    font-size: 1.7em; /* Reduced from 2em */
    display: block;
    margin-bottom: 8px; /* Reduced from 10px */
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 0.8em; /* Reduced from 0.9em */
    font-style: italic;
}

/* Animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Responsive - only stack on very small screens */
@media (max-width: 576px) {
    .progress-board {
        flex-direction: column !important;
    }
    
    .progress-board .col-md-4 {
        width: 100% !important;
        margin-bottom: 15px;
    }
    
    .progress-column {
        height: 350px;
    }
}

/* Scrollbar Styling */
.column-body::-webkit-scrollbar {
    width: 5px;
}

.column-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.column-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.column-body::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
@endsection
  </body>
</html>