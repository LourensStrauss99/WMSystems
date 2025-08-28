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
                    <h4 class="mb-3">{{ __('Change Password') }}</h4>
                    <form method="POST" action="{{ route('settings.password.update') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required autocomplete="current-password">
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password">
                        </div>
                        <div class="col-12">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary w-50">{{ __('Update Password') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
