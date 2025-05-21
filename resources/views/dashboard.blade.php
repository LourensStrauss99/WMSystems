<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
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
        <a
            href="/admin login"
            class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.591 1.009c1.64-.946 3.684.608 2.737 2.248a1.724 1.724 0 001.009 2.591c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.009 2.591c.946 1.64-.608 3.684-2.248 2.737a1.724 1.724 0 00-2.591 1.009c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.591-1.009c-1.64.946-3.684-.608-2.737-2.248a1.724 1.724 0 00-1.009-2.591c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.009-2.591c-.946-1.64.608-3.684 2.248-2.737a1.724 1.724 0 002.591-1.009z"
                />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
            </svg>
            Admin
        </a>
    </div>
</body>
</html>
