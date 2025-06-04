{{-- filepath: resources/views/customers.blade.php --}}
@extends('layouts.app')

@section('content')
    @include('layouts.nav')
    <div class="container mx-auto py-8">
        <div class="overflow-x-auto">
            <livewire:customers-table />
        </div>
    </div>
@endsection