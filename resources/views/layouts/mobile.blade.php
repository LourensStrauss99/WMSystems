<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile - @yield('title', 'App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; }
        .mobile-header { background: #007bff; color: #fff; padding: 1rem; text-align: center; font-size: 1.2rem; }
        .mobile-nav { display: flex; justify-content: space-around; background: #fff; border-top: 1px solid #ddd; position: fixed; bottom: 0; width: 100%; padding: 0.5rem 0; }
        .mobile-nav a { color: #007bff; text-decoration: none; font-weight: bold; }
        .mobile-content { padding: 1rem; padding-bottom: 3.5rem; }
    </style>
</head>
<body>
    <div class="mobile-header">
        @yield('header', 'Mobile App')
    </div>
    <div class="mobile-content">
        @yield('content')
    </div>
    <div class="mobile-nav">
        <a href="{{ route('mobile.jobcards.index') }}">Jobcards</a>
        <a href="{{ route('mobile.quotes.index') }}">Quotes</a>
    </div>
</body>
</html>
