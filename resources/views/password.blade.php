@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('settings') }}"
           class="btn btn-light border shadow-sm px-3 py-2 d-inline-flex align-items-center"
           title="Back to Settings">
            <i class="bi bi-arrow-left me-2" style="font-size: 1.3rem; color: #222;"></i>
            <span>Back to Settings</span>
        </a>
    </div>
    <h2 class="mb-3">Change Password</h2>
    <p class="text-muted mb-4">Ensure your account is using a long, random password to stay secure</p>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow rounded">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}" autocomplete="off">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('Current password') }}</label>
                            <input name="current_password" id="current_password" type="password" required autocomplete="current-password" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('New password') }}</label>
                            <input name="password" id="password" type="password" required autocomplete="new-password" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input name="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password" class="form-control" />
                        </div>
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
