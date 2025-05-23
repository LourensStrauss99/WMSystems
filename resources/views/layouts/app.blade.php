<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Workflow Management System') }}</title>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('/style.css') }}" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <div id="app">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container">
                <a class="navbar-brand" href="/">Workflow Management</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="/client">Client</a></li>
                        <li class="nav-item"><a class="nav-link" href="/jobcard">Jobcard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/progress">Progress</a></li>
                        <li class="nav-item"><a class="nav-link" href="/invoice">Invoices</a></li>
                        <li class="nav-item"><a class="nav-link" href="/artisanprogress">Artisan Progress</a></li>
                        <li class="nav-item"><a class="nav-link" href="/inventory">Inventory</a></li>
                        <li class="nav-item"><a class="nav-link" href="/reports">Reports</a></li>
                        <li class="nav-item"><a class="nav-link" href="/quotes">Quotes</a></li>
                        <li class="nav-item"><a class="nav-link" href="/settings">Settings</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
