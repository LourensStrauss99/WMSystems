<!DOCTYPE html>
<html>
<head>
    <title>Workflow Management - Landlord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-md">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Workflow Management System
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Multi-tenant management portal
                </p>
            </div>
            
            <div class="space-y-4">
                @auth
                    <div class="text-center">
                        <a href="{{ route('tenants.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Manage Tenants
                        </a>
                    </div>
                @else
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Login
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
