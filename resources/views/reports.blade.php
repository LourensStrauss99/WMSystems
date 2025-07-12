@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">📊 Business Reports Dashboard</h2>
        
        {{-- Month/Year Selector --}}
        <div class="d-flex gap-2">
            <select class="form-select" id="monthSelector" onchange="updateReport()">
                @foreach($availableMonths as $month)
                    <option value="{{ $month['value'] }}" {{ $month['value'] == $selectedMonth ? 'selected' : '' }}>
                        {{ $month['label'] }}
                    </option>
                @endforeach
            </select>
            
            <div class="btn-group" role="group">
                <button class="btn btn-outline-primary {{ $viewMode == 'monthly' ? 'active' : '' }}" 
                        onclick="changeViewMode('monthly')">Monthly</button>
                <button class="btn btn-outline-primary {{ $viewMode == 'ytd' ? 'active' : '' }}" 
                        onclick="changeViewMode('ytd')">Year to Date</button>
            </div>
        </div>
    </div>

    @php
        $currentData = $viewMode == 'ytd' ? $ytdData : $monthlyData;
        $currentRevenue = $viewMode == 'ytd' ? $ytdRevenue : $monthlyRevenue;
        $currentHours = $viewMode == 'ytd' ? $ytdHours : $monthlyHours;
    @endphp

    {{-- Enhanced Summary Cards --}}
    <div class="row mb-4">
        {{-- Hours Revenue Card --}}
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Hours Revenue</h6>
                            <h4 class="mb-0">R {{ number_format($currentRevenue['hours_revenue'], 2) }}</h4>
                            <small class="opacity-75">
                                {{ number_format($currentHours['booked_hours']) }} hours total
                            </small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                    <div class="mt-2">
                        <div class="small">
                            <div>Normal: R {{ number_format($currentRevenue['hours_breakdown']['normal'], 2) }}</div>
                            <div>Overtime: R {{ number_format($currentRevenue['hours_breakdown']['overtime'], 2) }}</div>
                            <div>Weekend: R {{ number_format($currentRevenue['hours_breakdown']['weekend'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventory Profit Card --}}
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Inventory Profit</h6>
                            <h4 class="mb-0">R {{ number_format($currentRevenue['inventory_profit'], 2) }}</h4>
                            <small class="opacity-75">
                                {{ $currentRevenue['inventory_margin'] }}% margin
                            </small>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-50"></i>
                    </div>
                    <div class="mt-2">
                        <div class="small">
                            <div>Revenue: R {{ number_format($currentRevenue['inventory_revenue'], 2) }}</div>
                            <div>Cost: R {{ number_format($currentRevenue['inventory_cost'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- VAT Card --}}
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">VAT ({{ $company->vat_percentage }}%)</h6>
                            <h4 class="mb-0">R {{ number_format($currentRevenue['vat_amount'], 2) }}</h4>
                            <small class="opacity-75">
                                On R {{ number_format($currentRevenue['subtotal'], 2) }}
                            </small>
                        </div>
                        <i class="fas fa-receipt fa-2x opacity-50"></i>
                    </div>
                    <div class="mt-2">
                        <div class="small">
                            <div>Total Inc VAT: R {{ number_format($currentRevenue['total_inc_vat'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Profit Card --}}
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Net Profit</h6>
                            <h4 class="mb-0">R {{ number_format($currentRevenue['net_profit'], 2) }}</h4>
                            <small class="opacity-75">
                                {{ $currentRevenue['profit_margin'] }}% margin
                            </small>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                    <div class="mt-2">
                        <div class="small">
                            <div>After all costs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hours Utilization Section --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Hours Utilization Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h3 text-primary">{{ number_format($currentHours['available_hours']) }}</div>
                            <small class="text-muted">Available Hours</small>
                            <div class="small">{{ $currentHours['working_days'] }} days × {{ $currentHours['artisan_count'] }} artisans</div>
                        </div>
                        <div class="col-4">
                            <div class="h3 text-success">{{ number_format($currentHours['booked_hours']) }}</div>
                            <small class="text-muted">Booked Hours</small>
                            <div class="small">{{ number_format($currentHours['hours_per_employee'], 1) }} hrs/employee</div>
                        </div>
                        <div class="col-4">
                            <div class="h3 text-info">{{ $currentHours['utilization_rate'] }}%</div>
                            <small class="text-muted">Utilization Rate</small>
                            <div class="small">
                                @if($currentHours['utilization_rate'] >= 80)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($currentHours['utilization_rate'] >= 60)
                                    <span class="badge bg-warning">Good</span>
                                @else
                                    <span class="badge bg-danger">Needs Improvement</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Progress Bar --}}
                    <div class="mt-3">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ min($currentHours['utilization_rate'], 100) }}%">
                                {{ $currentHours['utilization_rate'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hour Types Breakdown --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Hour Types & Rates
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Normal (R{{ number_format($company->standard_labour_rate, 2) }}/hr)</span>
                                    <strong>{{ number_format($currentRevenue['hours_detail']['normal']) }}h</strong>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $currentHours['booked_hours'] > 0 ? ($currentRevenue['hours_detail']['normal'] / $currentHours['booked_hours']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Overtime (R{{ number_format($company->calculateHourlyRate('overtime'), 2) }}/hr)</span>
                                    <strong>{{ number_format($currentRevenue['hours_detail']['overtime']) }}h</strong>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $currentHours['booked_hours'] > 0 ? ($currentRevenue['hours_detail']['overtime'] / $currentHours['booked_hours']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Weekend (R{{ number_format($company->calculateHourlyRate('weekend'), 2) }}/hr)</span>
                                    <strong>{{ number_format($currentRevenue['hours_detail']['weekend']) }}h</strong>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-info" style="width: {{ $currentHours['booked_hours'] > 0 ? ($currentRevenue['hours_detail']['weekend'] / $currentHours['booked_hours']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Holiday (R{{ number_format($company->calculateHourlyRate('holiday'), 2) }}/hr)</span>
                                    <strong>{{ number_format($currentRevenue['hours_detail']['holiday']) }}h</strong>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $currentHours['booked_hours'] > 0 ? ($currentRevenue['hours_detail']['holiday'] / $currentHours['booked_hours']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Employee Performance Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user-hard-hat me-2"></i>Employee Performance
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Total Hours</th>
                            <th>Jobcards</th>
                            <th>Avg Hours/Jobcard</th>
                            <th>Utilization</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employeeStats as $employee)
                            <tr>
                                <td>{{ $employee['name'] }}</td>
                                <td>{{ number_format($employee['total_hours'], 1) }}</td>
                                <td>{{ $employee['jobcard_count'] }}</td>
                                <td>{{ number_format($employee['avg_hours_per_jobcard'], 1) }}</td>
                                <td>
                                    @php
                                        $expectedHours = $viewMode == 'ytd' ? $currentHours['available_hours'] / $currentHours['artisan_count'] : $currentHours['available_hours'] / $currentHours['artisan_count'];
                                        $utilization = $expectedHours > 0 ? ($employee['total_hours'] / $expectedHours) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $utilization >= 80 ? 'bg-success' : ($utilization >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ min($utilization, 100) }}%">
                                            {{ number_format($utilization, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    

    {{-- Update your hours utilization display --}}
    <div class="text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $monthlyHours['artisan_count'] ?? 0 }}</div>
        <div class="text-xs text-gray-500">Total Employees</div>
    </div>
</div>

<script>
function updateReport() {
    const month = document.getElementById('monthSelector').value;
    const url = new URL(window.location.href);
    url.searchParams.set('month', month);
    window.location.href = url.toString();
}

function changeViewMode(mode) {
    const url = new URL(window.location.href);
    url.searchParams.set('view', mode);
    window.location.href = url.toString();
}
</script>
@endsection