<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Company - Workflow Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .registration-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .invalid-feedback {
            font-size: 0.875em;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="registration-card p-5">
                    <div class="text-center mb-5">
                        <i class="fas fa-building fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold">Register Your Company</h2>
                        <p class="text-muted">Start your 30-day free trial today</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Registration Failed!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tenant.register') }}">
                        @csrf

                        <div class="row">
                            <!-- Company Information -->
                            <div class="col-12">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-building me-2"></i>Company Information
                                </h5>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="company_name" class="form-label">
                                    <i class="fas fa-briefcase me-1"></i>Company Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" 
                                       name="company_name" 
                                       value="{{ old('company_name') }}" 
                                       required 
                                       autocomplete="organization"
                                       placeholder="Enter your company name">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Phone Number
                                </label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       autocomplete="tel"
                                       placeholder="Optional">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-label">&nbsp;</div>
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    We'll create a secure database for your company
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Address
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="3" 
                                          autocomplete="street-address"
                                          placeholder="Enter your company address (optional)">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Super User Information -->
                            <div class="col-12">
                                <h5 class="fw-bold mb-3 mt-4">
                                    <i class="fas fa-user-tie me-2"></i>Super User Account
                                </h5>
                                <p class="text-muted small mb-3">This will be the main administrator account for your company</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="owner_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Full Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('owner_name') is-invalid @enderror" 
                                       id="owner_name" 
                                       name="owner_name" 
                                       value="{{ old('owner_name') }}" 
                                       required 
                                       autocomplete="name"
                                       placeholder="Enter your full name">
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="owner_email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Address
                                </label>
                                <input type="email" 
                                       class="form-control @error('owner_email') is-invalid @enderror" 
                                       id="owner_email" 
                                       name="owner_email" 
                                       value="{{ old('owner_email') }}" 
                                       required 
                                       autocomplete="email"
                                       placeholder="Enter your email address">
                                @error('owner_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="owner_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('owner_password') is-invalid @enderror" 
                                       id="owner_password" 
                                       name="owner_password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Create a strong password">
                                @error('owner_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="owner_password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirm Password
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="owner_password_confirmation" 
                                       name="owner_password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Confirm your password">
                            </div>

                            <!-- Trial Information -->
                            <div class="col-12">
                                <div class="bg-light rounded-3 p-4 mb-4">
                                    <h6 class="fw-bold text-success">
                                        <i class="fas fa-gift me-2"></i>30-Day Free Trial
                                    </h6>
                                    <ul class="mb-0 text-muted small">
                                        <li>Full access to all features</li>
                                        <li>Unlimited jobcards and clients</li>
                                        <li>Secure isolated database</li>
                                        <li>No credit card required</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-rocket me-2"></i>Start Free Trial
                                </button>
                            </div>

                            <div class="col-12 text-center mt-3">
                                <p class="text-muted small mb-0">
                                    Already have an account? 
                                    <a href="{{ route('login') }}" class="text-decoration-none">Sign in here</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
