@extends('layouts.app')

@section('content')
    <div>
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search customers..." class="form-control mb-3">

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->surname }}</td>
                        <td>{{ $customer->email }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection