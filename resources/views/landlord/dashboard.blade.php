@extends('layouts.app')

@section('title', 'Landlord Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Landlord Dashboard</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('landlord.packages.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cube"></i> Packages
                    </a>
                    <a href="{{ route('landlord.income') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line"></i> Income Reports
                    </a>
                    <a href="{{ route('landlord.tenants.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users"></i> Manage Tenants
                    </a>
                    <a href="{{ route('landlord.communications.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-comments"></i> Communications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Monthly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R {{ number_format($monthlyRevenue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                Yearly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R {{ number_format($yearlyRevenue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Outstanding Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R {{ number_format($outstandingAmount, 2) }}
                            </div>
                            @if($overdueInvoices > 0)
                                <div class="text-xs text-danger">
                                    {{ $overdueInvoices }} overdue invoice{{ $overdueInvoices > 1 ? 's' : '' }}
                                </div>
                            @endif
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
                                Active Tenants
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activeTenants }} / {{ $totalTenants }}
                            </div>
                            @if($newTenantsThisMonth > 0)
                                <div class="text-xs text-success">
                                    +{{ $newTenantsThisMonth }} this month
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Package Revenue Breakdown -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Package Revenue Breakdown</h6>
                    <a href="{{ route('landlord.income') }}" class="btn btn-sm btn-primary">View Details</a>
                </div>
                <div class="card-body">
                    @if($packageRevenue->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Package</th>
                                        <th>Total Revenue</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalPackageRevenue = $packageRevenue->sum('total_revenue'); @endphp
                                    @foreach($packageRevenue as $package)
                                        @php $percentage = $totalPackageRevenue > 0 ? ($package->total_revenue / $totalPackageRevenue) * 100 : 0; @endphp
                                        <tr>
                                            <td>{{ $package->subscription_plan }}</td>
                                            <td>R {{ number_format($package->total_revenue, 2) }}</td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
                                                         style="width: {{ $percentage }}%" 
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small>{{ number_format($percentage, 1) }}%</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No revenue data available yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Communications -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Communications</h6>
                    <a href="{{ route('landlord.communications.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentCommunications->count() > 0)
                        @foreach($recentCommunications as $communication)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="mr-3">
                                    <div class="icon-circle bg-{{ $communication->priority === 'high' ? 'danger' : ($communication->priority === 'medium' ? 'warning' : 'success') }}">
                                        <i class="fas fa-{{ $communication->category === 'support' ? 'question-circle' : 'comments' }} text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-gray-500">{{ $communication->tenant->name ?? 'Unknown Tenant' }}</div>
                                    <div class="font-weight-bold">{{ $communication->subject }}</div>
                                    <div class="small text-muted">{{ $communication->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="badge badge-{{ $communication->status === 'resolved' ? 'success' : ($communication->status === 'open' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($communication->status) }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No recent communications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-sm {
    height: 0.5rem;
}

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
