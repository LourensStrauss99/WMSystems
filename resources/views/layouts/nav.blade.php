<header class="header">
    <div class="logo">Workflow-Management-Control</div>
    <nav class="tabs">
        <a href="/customers" class="tab-button {{ request()->is('customers*') ? 'active' : '' }}">Customers</a>
        <a href="/client" class="tab-button {{ request()->is('client*') ? 'active' : '' }}"> Orders</a>
        <a href="/jobcard" class="tab-button {{ request()->is('jobcard*') ? 'active' : '' }}"> Jobcard</a>
        <a href="/progress" class="tab-button {{ request()->is('progress*') ? 'active' : '' }}">Progress</a>
        <a href="/reports" class="tab-button {{ request()->is('reports*') ? 'active' : '' }}">Reports</a>
        <a href="/quotes" class="tab-button {{ request()->is('quotes*') ? 'active' : '' }}"> Quotes</a>
        <a href="/invoice" class="tab-button {{ request()->is('invoice*') ? 'active' : '' }}"> Invoices</a>
        <a href="/inventory" class="tab-button {{ request()->is('inventory*') ? 'active' : '' }}"> Inventory</a>
        <a href="/settings" class="tab-button {{ request()->is('settings*') ? 'active' : '' }}"> Settings</a>
    </nav>
</header>