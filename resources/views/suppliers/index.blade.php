{{-- filepath: resources/views/suppliers/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-industry me-2"></i>Suppliers</h2>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>New Supplier
        </a>
    </div>
    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->contact_person }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ $supplier->city }}</td>
                    <td>{!! $supplier->status_badge !!}</td>
                    <td class="text-end">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-info btn-sm" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        {{-- Replace the existing delete form with this enhanced version --}}
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger btn-sm delete-btn" 
                                    title="Delete" data-supplier-name="{{ $supplier->name }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No suppliers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div>
        {{ $suppliers->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const supplierName = this.dataset.supplierName;
            const form = this.closest('.delete-form');
            
            if (confirm(`Are you sure you want to delete supplier "${supplierName}"?\n\nThis action cannot be undone.`)) {
                form.submit();
            }
        });
    });
});
</script>

@endsection