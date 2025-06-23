@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <h2 class="mb-3">📊 Business Reports Dashboard</h2>
    
    <!-- Summary Cards Row -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-2">
                    <h6 class="mb-1">Total Revenue</h6>
                    <h4 class="mb-0">R {{ number_format($invoicesGrandTotal, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center py-2">
                    <h6 class="mb-1">Total Jobcards</h6>
                    <h4 class="mb-0">{{ $totalJobcards }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center py-2">
                    <h6 class="mb-1">Hours Utilization</h6>
                    <h4 class="mb-0">{{ $hoursData['utilization_rate'] }}%</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center py-2">
                    <h6 class="mb-1">Inventory Profit</h6>
                    <h4 class="mb-0">R {{ number_format($inventoryReport['total_profit'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Graph Windows Row -->
    <div class="row" style="height: 600px;">
        
        <!-- Jobcard Status Graph Window -->
        <div class="col-md-6 mb-3">
            <div class="graph-window">
                <div class="graph-header">
                    <h6>📈 Jobcard Status Overview</h6>
                    <div class="graph-controls">
                        <span class="status-indicator"></span>
                        <button class="graph-btn">⚙️</button>
                    </div>
                </div>
                <div class="graph-content">
                    <canvas id="jobcardStatusChart"></canvas>
                </div>
                <div class="graph-footer">
                    <div class="stats-row">
                        <div class="stat-item">
                            <span class="stat-label">Assigned</span>
                            <span class="stat-value">{{ $jobcardStatus['assigned'] }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">In Progress</span>
                            <span class="stat-value">{{ $jobcardStatus['in_progress'] }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Completed</span>
                            <span class="stat-value">{{ $jobcardStatus['completed'] }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Invoiced</span>
                            <span class="stat-value">{{ $jobcardStatus['invoiced'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Aging Graph Window -->
        <div class="col-md-6 mb-3">
            <div class="graph-window">
                <div class="graph-header">
                    <h6>💰 Invoice Aging Analysis</h6>
                    <div class="graph-controls">
                        <span class="status-indicator warning"></span>
                        <button class="graph-btn">📊</button>
                    </div>
                </div>
                <div class="graph-content">
                    <canvas id="invoiceAgingChart"></canvas>
                </div>
                <div class="graph-footer">
                    <div class="stats-row">
                        <div class="stat-item">
                            <span class="stat-label">Paid Amount</span>
                            <span class="stat-value">R{{ number_format($invoiceAging['total_paid_amount'], 0) }}</span>
                        </div>
                        <div class="stat-item critical">
                            <span class="stat-label">120+ Days</span>
                            <span class="stat-value">{{ $invoiceAging['unpaid_120_plus'] }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Outstanding</span>
                            <span class="stat-value">R{{ number_format($invoiceAging['total_unpaid_amount'], 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hours Productivity Graph Window -->
        <div class="col-md-6 mb-3">
            <div class="graph-window">
                <div class="graph-header">
                    <h6>⏰ Hours Productivity Analysis</h6>
                    <div class="graph-controls">
                        <span class="status-indicator success"></span>
                        <button class="graph-btn">📈</button>
                    </div>
                </div>
                <div class="graph-content">
                    <canvas id="hoursChart"></canvas>
                </div>
                <div class="graph-footer">
                    <div class="stats-row">
                        <div class="stat-item">
                            <span class="stat-label">Available</span>
                            <span class="stat-value">{{ $hoursData['available_hours'] }}h</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Booked</span>
                            <span class="stat-value">{{ $hoursData['booked_hours'] }}h</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Utilization</span>
                            <span class="stat-value">{{ $hoursData['utilization_rate'] }}%</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Artisans</span>
                            <span class="stat-value">{{ $hoursData['total_artisans'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Profit Graph Window -->
        <div class="col-md-6 mb-3">
            <div class="graph-window">
                <div class="graph-header">
                    <h6>📦 Inventory Cost vs Income</h6>
                    <div class="graph-controls">
                        <span class="status-indicator success"></span>
                        <button class="graph-btn">💹</button>
                    </div>
                </div>
                <div class="graph-content">
                    <canvas id="inventoryChart"></canvas>
                </div>
                <div class="graph-footer">
                    <div class="stats-row">
                        <div class="stat-item">
                            <span class="stat-label">Total Cost</span>
                            <span class="stat-value">R{{ number_format($inventoryReport['total_cost'], 0) }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Total Income</span>
                            <span class="stat-value">R{{ number_format($inventoryReport['total_income'], 0) }}</span>
                        </div>
                        <div class="stat-item profit">
                            <span class="stat-label">Profit Margin</span>
                            <span class="stat-value">{{ $inventoryReport['total_income'] > 0 ? round(($inventoryReport['total_profit'] / $inventoryReport['total_income']) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js Global Configuration
Chart.defaults.font.family = 'Arial, sans-serif';
Chart.defaults.font.size = 11;

// Jobcard Status Chart (Bar Chart)
const jobcardCtx = document.getElementById('jobcardStatusChart').getContext('2d');
new Chart(jobcardCtx, {
    type: 'bar',
    data: {
        labels: ['Assigned', 'In Progress', 'Completed', 'Invoiced'],
        datasets: [{
            label: 'Jobcards',
            data: [
                {{ $jobcardStatus['assigned'] }},
                {{ $jobcardStatus['in_progress'] }},
                {{ $jobcardStatus['completed'] }},
                {{ $jobcardStatus['invoiced'] }}
            ],
            backgroundColor: [
                '#ffc107',  // Yellow for Assigned
                '#fd7e14',  // Orange for In Progress  
                '#28a745',  // Green for Completed
                '#007bff'   // Blue for Invoiced
            ],
            borderColor: [
                '#e0a800',
                '#e8690b', 
                '#1e7e34',
                '#0056b3'
            ],
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                display: false  // Hide legend since we have labels on x-axis
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed.y + ' jobcards';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { 
                    color: '#e9ecef',
                    drawBorder: false
                },
                ticks: {
                    stepSize: 1,  // Show whole numbers only
                    color: '#6c757d'
                }
            },
            x: {
                grid: { 
                    display: false 
                },
                ticks: {
                    color: '#6c757d',
                    maxRotation: 45,
                    minRotation: 0
                }
            }
        },
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Invoice Aging Chart (Bar)
const agingCtx = document.getElementById('invoiceAgingChart').getContext('2d');
new Chart(agingCtx, {
    type: 'bar',
    data: {
        labels: ['Paid', '0-30', '30-60', '60-90', '90-120', '120+'],
        datasets: [{
            label: 'Invoices',
            data: [
                {{ $invoiceAging['paid'] }},
                {{ $invoiceAging['unpaid_current'] }},
                {{ $invoiceAging['unpaid_30_days'] }},
                {{ $invoiceAging['unpaid_60_days'] }},
                {{ $invoiceAging['unpaid_90_days'] }},
                {{ $invoiceAging['unpaid_120_plus'] }}
            ],
            backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1'],
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#e9ecef' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// Hours Productivity Chart (Changed from Line to Bar)
const hoursCtx = document.getElementById('hoursChart').getContext('2d');
new Chart(hoursCtx, {
    type: 'bar',
    data: {
        labels: ['Available Hours', 'Booked Hours', 'Invoiced Hours'],
        datasets: [{
            label: 'Hours',
            data: [
                {{ $hoursData['available_hours'] }},
                {{ $hoursData['booked_hours'] }},
                {{ $hoursData['invoiced_hours'] }}
            ],
            backgroundColor: [
                '#17a2b8',  // Teal for Available
                '#007bff',  // Blue for Booked
                '#28a745'   // Green for Invoiced
            ],
            borderColor: [
                '#138496',
                '#0056b3',
                '#1e7e34'
            ],
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed.y + ' hours';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { 
                    color: '#e9ecef',
                    drawBorder: false
                },
                ticks: {
                    color: '#6c757d',
                    callback: function(value) {
                        return value + 'h';
                    }
                }
            },
            x: {
                grid: { 
                    display: false 
                },
                ticks: {
                    color: '#6c757d',
                    maxRotation: 45,
                    minRotation: 0
                }
            }
        },
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Inventory Chart (Horizontal Bar)
const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
new Chart(inventoryCtx, {
    type: 'bar',
    data: {
        labels: ['Cost', 'Income', 'Profit'],
        datasets: [{
            data: [
                {{ $inventoryReport['total_cost'] }},
                {{ $inventoryReport['total_income'] }},
                {{ $inventoryReport['total_profit'] }}
            ],
            backgroundColor: ['#dc3545', '#007bff', '#28a745'],
            borderRadius: 4
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: '#e9ecef' }
            },
            y: {
                grid: { display: false }
            }
        }
    }
});
</script>

<style>
/* Graph Window Styling */
.graph-window {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: 280px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.graph-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    padding: 8px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 40px;
}

.graph-header h6 {
    margin: 0;
    font-size: 0.9em;
    font-weight: 600;
    color: #495057;
}

.graph-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28a745;
    animation: pulse 2s infinite;
}

.status-indicator.warning {
    background: #ffc107;
}

.status-indicator.critical {
    background: #dc3545;
}

.graph-btn {
    background: none;
    border: none;
    font-size: 0.8em;
    color: #6c757d;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 3px;
}

.graph-btn:hover {
    background: #e9ecef;
}

.graph-content {
    flex: 1;
    padding: 15px;
    position: relative;
}

.graph-footer {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 8px 15px;
    min-height: 50px;
}

.stats-row {
    display: flex;
    justify-content: space-around;
    align-items: center;
}

.stat-item {
    text-align: center;
    flex: 1;
}

.stat-label {
    display: block;
    font-size: 0.7em;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 500;
}

.stat-value {
    display: block;
    font-size: 0.9em;
    font-weight: 700;
    color: #495057;
    margin-top: 2px;
}

.stat-item.critical .stat-value {
    color: #dc3545;
}

.stat-item.profit .stat-value {
    color: #28a745;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 15px;
    }
    
    .graph-window {
        height: 250px;
    }
    
    .stats-row {
        flex-wrap: wrap;
    }
    
    .stat-item {
        flex: 0 0 50%;
        margin-bottom: 5px;
    }
}
</style>
@endsection