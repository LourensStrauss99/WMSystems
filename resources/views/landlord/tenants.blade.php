<!DOCTYPE html>
<html>
<head>
    <title>Tenant Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Tenant Management</h1>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <!-- Create Tenant Form -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold mb-4">Create New Tenant</h2>
                        <form method="POST" action="{{ route('landlord.tenants.store') }}" class="bg-white p-6 rounded-lg shadow">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Company Information -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                                    <input type="text" name="name" placeholder="Company Name" 
                                           class="w-full border border-gray-300 rounded px-3 py-2" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tenant ID *</label>
                                    <input type="text" name="tenant_id" placeholder="tenant-id (lowercase, no spaces)" 
                                           class="w-full border border-gray-300 rounded px-3 py-2" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                                    <input type="text" name="domain" placeholder="subdomain.workflow-management.test" 
                                           class="w-full border border-gray-300 rounded px-3 py-2">
                                </div>
                                
                                <!-- Owner Information -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name *</label>
                                    <input type="text" name="owner_name" placeholder="John Smith" 
                                           class="w-full border border-gray-300 rounded px-3 py-2" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Owner Email *</label>
                                    <input type="email" name="owner_email" placeholder="owner@company.com" 
                                           class="w-full border border-gray-300 rounded px-3 py-2" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Owner Password *</label>
                                    <input type="password" name="owner_password" placeholder="Strong password" 
                                           class="w-full border border-gray-300 rounded px-3 py-2" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Owner Phone</label>
                                    <input type="tel" name="owner_phone" placeholder="+1234567890" 
                                           class="w-full border border-gray-300 rounded px-3 py-2">
                                </div>
                                
                                <!-- Company Address -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" name="address" placeholder="123 Business St" 
                                           class="w-full border border-gray-300 rounded px-3 py-2">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <input type="text" name="city" placeholder="Business City" 
                                           class="w-full border border-gray-300 rounded px-3 py-2">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <input type="text" name="country" placeholder="South Africa" value="South Africa"
                                           class="w-full border border-gray-300 rounded px-3 py-2">
                                </div>
                                
                                <!-- Payment Information -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Fee (R)</label>
                                    <input type="number" name="monthly_fee" placeholder="999.00" step="0.01" min="0"
                                           class="w-full border border-gray-300 rounded px-3 py-2">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Plan</label>
                                    <select name="subscription_plan" class="w-full border border-gray-300 rounded px-3 py-2">
                                        <option value="basic">Basic</option>
                                        <option value="standard">Standard</option>
                                        <option value="premium">Premium</option>
                                        <option value="enterprise">Enterprise</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                    Create Tenant & Setup Database
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Existing Tenants -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Existing Tenants</h2>
                        @if($tenants->count() > 0)
                            <div class="grid gap-4">
                                @foreach($tenants as $tenant)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h3 class="font-semibold">{{ $tenant->id }}</h3>
                                                @if($tenant->domains->first())
                                                    <p class="text-sm text-gray-600">
                                                        Domain: {{ $tenant->domains->first()->domain }}
                                                    </p>
                                                    <div class="mt-2 flex gap-3 items-center">
                                                        <a href="http://{{ $tenant->domains->first()->domain }}" 
                                                           target="_blank"
                                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                                            Visit Tenant →
                                                        </a>
                                                        <a href="{{ route('landlord.tenants.impersonate', $tenant) }}"
                                                           class="text-indigo-600 hover:text-indigo-800 text-sm">
                                                            Impersonate Owner
                                                        </a>
                                                        <a href="{{ route('landlord.tenants.payments', $tenant) }}"
                                                           class="text-emerald-600 hover:text-emerald-800 text-sm">
                                                            View Payments
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Created: {{ $tenant->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600">No tenants created yet.</p>
                        @endif
                    </div>
                    
                    <div class="mt-8">
                        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
