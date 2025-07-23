@extends('layouts.app')
@extends('layouts.nav')
@section('content')
<div class="container bg-white p-5 rounded shadow" style="max-width: 900px; margin: auto; font-family: 'Arial', sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quotes</h2>
        <a href="{{ route('quotes.create') }}" class="btn btn-primary">Create New Quote</a>
    </div>
    <livewire:quote-list />
</div>
@endsection