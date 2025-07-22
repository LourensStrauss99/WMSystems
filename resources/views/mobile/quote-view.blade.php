@extends('layouts.mobile')

@section('header', 'Quotes')

@section('content')
    <h2>Quotes</h2>
    <ul>
        @foreach($quotes as $quote)
            <li>
                {{ $quote->quote_number }}
                <a href="{{ route('mobile.quotes.edit', $quote->id) }}">Edit</a>
            </li>
        @endforeach
    </ul>
@endsection 