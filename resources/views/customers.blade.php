{{-- filepath: resources/views/customers.blade.php --}}
@extends('layouts.app')

@section('content')
    @include('layouts.nav')

    <div class="container mx-auto py-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">View Customers</h2>
            <a href="{{ route('client.create') }}" class="bg-blue-400 text-white px-4 py-2 rounded hover:bg-blue-500">+ Add Customer</a>
        </div>
        <div class="overflow-x-auto">
            <livewire:customers-table />
        </div>
    </div>
@endsection