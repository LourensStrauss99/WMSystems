<aside class="sidebar">
    <div class="logo">Workflow-Management-Control</div>
    <nav class="sidebar-tabs">
        <!-- ✅ ADD DASHBOARD LINK -->
        <a href="/dashboard" class="sidebar-tab {{ request()->is('dashboard*') ? 'active' : '' }}">Dashboard</a>
        
        <a href="/customers" class="sidebar-tab {{ request()->is('customers*') ? 'active' : '' }}">Customers</a>
        <a href="/client" class="sidebar-tab {{ request()->is('client*') ? 'active' : '' }}">Orders</a>
        <a href="/jobcard" class="sidebar-tab {{ request()->is('jobcard*') ? 'active' : '' }}">Jobcard</a>
        <a href="/progress" class="sidebar-tab {{ request()->is('progress*') ? 'active' : '' }}">Progress</a>
        <a href="/reports" class="sidebar-tab {{ request()->is('reports*') ? 'active' : '' }}">Reports</a>
        <a href="/quotes" class="sidebar-tab {{ request()->is('quotes*') ? 'active' : '' }}">Quotes</a>
        <a href="/invoice" class="sidebar-tab {{ request()->is('invoice*') ? 'active' : '' }}">Invoices</a>
        <a href="/inventory" class="sidebar-tab {{ request()->is('inventory*') ? 'active' : '' }}">Inventory</a>
        <a href="/settings" class="sidebar-tab {{ request()->is('settings*') ? 'active' : '' }}">Settings</a>
        <a href="/mobile/jobcards" class="sidebar-tab">Mobile</a>
        
        {{-- Approvals link, visible only to users with approval permissions --}}
        @if(auth()->check() && method_exists(auth()->user(), 'canApprove') && auth()->user()->canApprove())
            <a href="{{ route('approvals.index') }}" class="sidebar-tab">
                Approvals
                @if($pendingCount = \App\Models\PurchaseOrder::where('status', 'pending_approval')->count())
                    <span class="badge bg-warning">{{ $pendingCount }}</span>
                @endif
            </a>
        @endif
    </nav>
</aside>

<style>
.sidebar {
    width: 220px;
    min-height: 100vh;
    background: #1e293b;
    color: #fff;
    position: fixed;
    left: 0;
    top: 0;
    padding: 2rem 1rem 1rem 1rem;
    display: flex;
    flex-direction: column;
    z-index: 100;
}
.logo {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 2rem;
    text-align: center;
}
.sidebar-tabs {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.sidebar-tab {
    color: #cbd5e1;
    text-decoration: none;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    transition: background 0.2s, color 0.2s;
}
.sidebar-tab.active,
.sidebar-tab:hover {
    background: #3b82f6;
    color: #fff;
}
</style>