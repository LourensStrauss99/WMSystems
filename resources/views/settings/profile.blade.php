@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('settings') }}" class="btn btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left"></i> {{ __('Back to Settings') }}
            </a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">{{ __('Profile') }}</h4>
                    <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name">
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required autocomplete="email">
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">{{ __('Telephone') }}</label>
                            <input type="text" name="telephone" id="telephone" class="form-control" value="{{ old('telephone', auth()->user()->telephone) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">{{ __('Address') }}</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', auth()->user()->address) }}">
                        </div>
                        <div class="col-12">
                            <label for="photo" class="form-label">{{ __('Badge Photo') }}</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Badge Photo" class="mt-2 rounded-circle border border-primary shadow" style="width: 80px; height: 80px; object-fit: cover;">
                            @endif
                            @error('photo') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary w-50">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
