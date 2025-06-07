{{-- filepath: resources/views/jobcard/test.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Test Blade File Loaded!</h2>
        <p>If you see this, Blade rendering works.</p>
    </div>
    <livewire:test-component />
@endsection