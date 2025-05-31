  <header class="header">
            <div class="logo">Workflow-Management-Control</div>
            <nav class="tabs">
              <a href="/client" class="tab-button {{ request()->is('client*') ? 'active' : '' }}">1 - Client</a>
              <a href="/jobcard" class="tab-button {{ request()->is('jobcard*') ? 'active' : '' }}">2 - Jobcard</a>
              <a href="/progress" class="tab-button {{ request()->is('progress*') ? 'active' : '' }}">3 - Progress</a>
              <a href="/reports" class="tab-button {{ request()->is('reports*') ? 'active' : '' }}">4 - Reports</a>
              <a href="/quotes" class="tab-button {{ request()->is('quotes*') ? 'active' : '' }}">5 - Quotes</a>
              <a href="/invoice" class="tab-button {{ request()->is('invoice*') ? 'active' : '' }}">6 - Invoices</a>
              <a href="/inventory" class="tab-button {{ request()->is('inventory*') ? 'active' : '' }}">8 - Inventory</a>
              <a href="/settings" class="tab-button {{ request()->is('settings*') ? 'active' : '' }}">7 - Settings</a>
            </nav>

        </header>