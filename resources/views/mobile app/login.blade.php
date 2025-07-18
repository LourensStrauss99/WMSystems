@extends('layouts.mobile')
@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Mobile Login</h2>
    <form method="POST" action="/mobile-app/login" class="card p-4 shadow-sm">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        @if($errors->any())
            <div class="alert alert-danger small">{{ $errors->first() }}</div>
        @endif
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
@endsection