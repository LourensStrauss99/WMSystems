
<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisterController; // Add this
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register'); // Update this

    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::get('reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');
});

Route::post('logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::post('register', [RegisterController::class, 'register'])->name('register');
Route::post('register', [RegisterController::class, 'createUser'])->name('register');

Route::post('login', [LoginController::class, 'login'])->name('login');