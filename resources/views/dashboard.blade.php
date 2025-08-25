<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Dashboard</h1>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-8 p-4 text-center">
        <h2 class="text-2xl font-semibold mb-4">Welcome to Your Dashboard</h2>
        <p class="text-gray-700 mb-6">
            Navigate using the buttons below.
        </p>

        <!-- Home Button -->
        <a
            href="/client"
            class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 mb-4"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5-5h3m-3 0a2 2 0 01-2 2m-4 0a2 2 0 01-2-2m-4 0H3m3 0a2 2 0 012-2m4 0a2 2 0 012 2"
                />
            </svg>
            Home
        </a>

        <!-- Admin Button -->
        <a href="/master-settings"
           class="inline-flex items-center bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 mb-4 ml-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11.25 2.25c.38-1.01 1.87-1.01 2.25 0a1.5 1.5 0 002.1.83c.94-.54 2.01.53 1.47 1.47a1.5 1.5 0 00.83 2.1c1.01.38 1.01 1.87 0 2.25a1.5 1.5 0 00-.83 2.1c.54.94-.53 2.01-1.47 1.47a1.5 1.5 0 00-2.1.83c-.38 1.01-1.87 1.01-2.25 0a1.5 1.5 0 00-2.1-.83c-.94.54-2.01-.53-1.47-1.47a1.5 1.5 0 00-.83-2.1c-1.01-.38-1.01-1.87 0-2.25a1.5 1.5 0 00.83-2.1c-.54-.94.53-2.01 1.47-1.47a1.5 1.5 0 002.1-.83z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Master Settings
        </a>

        <!-- Landlord Button (Only for Super Admin Level 10) -->
        @auth
            @if(auth()->user()->admin_level == 10 && auth()->user()->is_landlord == 1)
                    <!-- Removed: Landlord Button (Only for Super Admin Level 10) -->
                    {{-- Reference: landlord, super-admin logic removed --}}
            @endif
        @endauth

        @if(session('admin_error'))
            <div class="alert alert-danger text-red-600 font-bold mb-4">
                {{ session('admin_error') }}
            </div>
        @endif

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit"
                class="inline-flex items-center bg-red-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-red-700 mb-4 ml-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V5m0 0a2 2 0 00-2-2h-4a2 2 0 00-2 2v14a2 2 0 002 2h4a2 2 0 002-2v-1"
                    />
                </svg>
                Log Out
            </button>
        </form>
    </div>
</body>
</html>
