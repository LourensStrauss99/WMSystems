<!DOCTYPE html>
<html>
<head>
    <title>Tenant Payments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Payments for {{ $tenant->name }} ({{ $tenant->id }})</h1>

                    <div class="mb-4 flex items-center gap-3">
                        @if($tenant->domains->first())
                            <a class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" target="_blank" href="http://{{ $tenant->domains->first()->domain }}">Visit Tenant</a>
                            <a class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" href="{{ route('landlord.tenants.impersonate', $tenant) }}">Impersonate Owner</a>
                        @endif
                        <a class="text-blue-600 hover:text-blue-800" href="{{ route('landlord.tenants.index') }}">← Back</a>
                    </div>

                    @if($payments->count() === 0)
                        <p class="text-gray-600">No payment history yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($payments as $p)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $p->paid_at ?? $p->created_at }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700">R {{ number_format((float)$p->amount, 2) }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $p->method ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $p->status }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $p->reference ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
