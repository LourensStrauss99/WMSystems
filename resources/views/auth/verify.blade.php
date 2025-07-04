<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Account Verification Required</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Account Verification Required
                        </h4>
                    </div>
                    <div class="card-body">
                        {{-- Success Messages --}}
                        @if(session('message'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                            </div>
                        @endif

                        {{-- Testing Code Display --}}
                        @if(session('verification_code'))
                            <div class="alert alert-info">
                                <i class="fas fa-code me-2"></i>{{ session('verification_code') }}
                            </div>
                        @endif

                        {{-- Error Messages --}}
                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <div><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <p class="text-muted mb-4">
                            Welcome {{ $user->name }}! To ensure account security, please verify your contact information.
                        </p>

                        {{-- Email Verification --}}
                        <div class="mb-4">
                            <h6 class="d-flex align-items-center">
                                @if($user->hasVerifiedEmail())
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                @else
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                @endif
                                Email Verification
                            </h6>
                            
                            @if($user->hasVerifiedEmail())
                                <p class="text-success small mb-0">
                                    <i class="fas fa-envelope me-1"></i>{{ $user->email }} is verified
                                </p>
                            @else
                                <p class="text-muted small">{{ $user->email }}</p>
                                <form action="{{ route('verification.send') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-paper-plane me-1"></i>Resend Verification Email
                                    </button>
                                </form>
                            @endif
                        </div>

                        {{-- Phone Verification --}}
                        @if($user->telephone)
                        <div class="mb-4">
                            <h6 class="d-flex align-items-center">
                                @if($user->hasVerifiedPhone())
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                @else
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                @endif
                                Phone Verification
                            </h6>
                            
                            @if($user->hasVerifiedPhone())
                                <p class="text-success small mb-0">
                                    <i class="fas fa-phone me-1"></i>{{ $user->telephone }} is verified
                                </p>
                            @else
                                <p class="text-muted small">{{ $user->telephone }}</p>
                                <div class="row g-2">
                                    <div class="col-auto">
                                        <form action="{{ route('verification.phone.send') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-sms me-1"></i>Send Code
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col">
                                        <form action="{{ route('verification.phone.verify') }}" method="POST" class="d-flex">
                                            @csrf
                                            <input type="text" name="verification_code" class="form-control form-control-sm me-2" 
                                                   placeholder="Enter 6-digit code" maxlength="6" pattern="[0-9]{6}">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check me-1"></i>Verify
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif

                        {{-- Navigation --}}
                        <div class="d-grid gap-2">
                            @if($user->hasVerifiedEmail() && ($user->hasVerifiedPhone() || !$user->telephone))
                                <a href="{{ route('dashboard') }}" class="btn btn-success">
                                    <i class="fas fa-arrow-right me-2"></i>Continue to Dashboard
                                </a>
                            @else
                                <p class="text-muted small text-center">
                                    Complete verification to access your account
                                </p>
                            @endif

                            {{-- Testing Bypass (Local Environment Only) --}}
                            @if(app()->environment('local', 'testing'))
                                <a href="{{ route('verification.bypass') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-skip-forward me-2"></i>Skip Verification (Testing)
                                </a>
                            @endif

                            <a href="{{ route('logout') }}" class="btn btn-outline-danger"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
