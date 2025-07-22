@extends('layouts.mobile')

@section('header', 'Edit Quote')

@section('content')
    <h2>Edit Quote</h2>
    <form>
        <div>Quote #: {{ $quote->quote_number }}</div>
        <!-- Add more fields as needed -->
        <a href="{{ route('mobile.quotes.index') }}">Back to Quotes</a>
    </form>
@endsection 