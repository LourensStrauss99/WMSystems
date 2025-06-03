<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client</title>
    <link rel="stylesheet" href="/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    @include('layouts.nav')
   
    <div class="flex justify-center items-center min-h-screen bg-gray-100" style="background: url('/your-bg.jpg') no-repeat center center fixed; background-size: cover;">
        <div class="bg-white p-8 rounded shadow" style="max-width: 900px; width: 100%;">
            <h2 class="text-2xl font-semibold mb-4 text-center">Client Details</h2>
            <livewire:client-form />
        </div>
    </div>
    
    @livewireScripts
</body>
</html>

