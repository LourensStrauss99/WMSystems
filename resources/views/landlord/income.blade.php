@extends('layouts.app')

@section('title', 'Income Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Income Analytics</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('landlord.income') }}" class="row align-items-end">
                        <div class="col-md-4">
                            <label for="year" class="form-label">Year</label>
                            <select name="year" id="year" class="form-control">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="month" class="form-label">Month (Optional)</label>
                            <select name="month" id="month" class="form-control">
                                <option value="">All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('landlord.income') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Trend ({{ $year }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Summary Statistics</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalRevenue = array_sum($monthlyData);
                        $averageMonthly = $totalRevenue / 12;
                        $maxMonth = max($monthlyData);
                        $minMonth = min($monthlyData);
                    @endphp
                    
                    <div class="mb-3">
                        <div class="small text-muted">Total Revenue ({{ $year }})</div>
                        <div class="h4 text-success">R {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">Average Monthly</div>
                        <div class="h5">R {{ number_format($averageMonthly, 2) }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">Best Month</div>
                        <div class="h5 text-primary">R {{ number_format($maxMonth, 2) }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">Lowest Month</div>
                        <div class="h5 text-warning">R {{ number_format($minMonth, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Revenue Breakdown -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Package Revenue Breakdown 
                        @if($month)
                            - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
                        @else
                            - {{ $year }}
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($packageBreakdown->count() > 0)
                        <div class="row">
                            @foreach($packageBreakdown as $package)
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        {{ $package->name }}
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        R {{ number_format($package->total_revenue, 2) }}
                                                    </div>
                                                    <div class="text-xs text-muted">
                                                        {{ $package->tenant_count }} tenant{{ $package->tenant_count != 1 ? 's' : '' }}
                                                    </div>
                                                    <div class="text-xs text-success">
                                                        R {{ number_format($package->monthly_price, 2) }}/month
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No package revenue data available for the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Tenant</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>
                                                <code>{{ $payment->payment_reference }}</code>
                                            </td>
                                            <td>{{ $payment->tenant->name ?? 'Unknown' }}</td>
                                            <td>
                                                <span class="font-weight-bold">
                                                    {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ ucfirst($payment->payment_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success" title="Generate Receipt">
                                                        <i class="fas fa-receipt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $payments->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No payment history available for the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Revenue Chart
    const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue (R)',
                data: monthlyData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Revenue Trend'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'R' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
