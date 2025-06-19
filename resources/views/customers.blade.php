{{-- filepath: resources/views/customers.blade.php --}}
@extends('layouts.app')

@section('content')
    
    <div class="container mx-auto py-8">
        <div class="flex justify-between items-center mb-4">
            <div></div>
            <div class="flex items-center gap-2">
                @if(request('search'))
                    <a href="{{ route('customers.index') }}"
                       class="flex items-center px-3 py-2 rounded border border-graysearch and view updated-300 bg-white text-black font-semibold shadow hover:bg-gray-100 transition"
                       title="Back to Customers">
                        <i class="bi bi-arrow-left" style="font-size: 1.3rem; color: #222;"></i>
                    </a>
                @endif
                <a href="{{ route('client.create') }}"
                   class="flex items-center px-4 py-2 rounded border border-blue-300 bg-blue-200 text-black font-semibold shadow hover:bg-blue-300 transition"
                   style="gap: 0.5rem;">
                    <i class="bi bi-person" style="font-size: 1.3rem; color: #3b82f6; background: #e0edff; border-radius: 50%; padding: 0.2rem;"></i>
                    Add New Customer
                </a>
            </div>
        </div>
        <form method="GET" action="{{ route('customers.index') }}" class="mb-4 flex items-center">
            <input type="text" name="search" value="{{ old('search', $search ?? '') }}" placeholder="Search"
                   class="border rounded px-2 py-1 mr-2" />
            <select name="perPage" class="border rounded px-2 py-1 mr-2" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
            </select>
            <button type="submit"
                class="flex items-center px-4 py-1 rounded bg-blue-200 text-black font-semibold shadow hover:bg-blue-300 transition"
                style="gap: 0.5rem;">
                <i class="bi bi-search" style="font-size: 1.1rem;"></i>
                Name Search
            </button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">ID</th>
                        <th class="px-4 py-2 border-b">Name</th>
                        <th class="px-4 py-2 border-b">Surname</th>
                        <th class="px-4 py-2 border-b">Telephone</th>
                        <th class="px-4 py-2 border-b">Address</th>
                        <th class="px-4 py-2 border-b">Email</th>
                        <th class="px-4 py-2 border-b">View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 border-b">{{ $customer->id }}</td>
                        <td class="px-4 py-2 border-b">{{ $customer->name }}</td>
                        <td class="px-4 py-2 border-b">{{ $customer->surname }}</td>
                        <td class="px-4 py-2 border-b">{{ $customer->telephone }}</td>
                        <td class="px-4 py-2 border-b">{{ $customer->address }}</td>
                        <td class="px-4 py-2 border-b">{{ $customer->email }}</td>
                        <td class="px-4 py-2 border-b text-center">
                            <a href="{{ route('client.show', $customer->id) }}" 
                               class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                               style="width:2rem; height:2rem; background-color:#649ff7; color:white;" 
                               title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $customers->links('pagination::tailwind') }}
            </div>
        </div>
       
    </div>
@endsection