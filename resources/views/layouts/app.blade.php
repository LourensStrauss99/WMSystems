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
    <style>
        .tr-highlight {
            background-color: #e0e7ff !important; /* Light blue, change as needed */
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Navbar Start -->
      
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
    <script>
function highlightRow(row) {
    // Remove highlight from all rows
    document.querySelectorAll('tr.tr-highlight').forEach(function(tr) {
        tr.classList.remove('tr-highlight');
    });
    // Add highlight to the clicked row
    row.classList.add('tr-highlight');
}
</script>
</body>
</html>
