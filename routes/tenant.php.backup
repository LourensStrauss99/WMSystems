<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Import your existing controllers
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\JobcardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrvController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MobileJobcardPhotoController;
use App\Http\Controllers\MobileAuthController;
use Illuminate\Support\Facades\Log;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    // Tenant Dashboard
    Route::get('/', function () {
        return view('dashboard', ['tenant_id' => tenant('id')]);
    })->middleware(['auth'])->name('tenant.dashboard');

    // Authentication Routes
    Auth::routes(['verify' => true]);

    // Dashboard Route
    Route::get('/dashboard', function () {
        return view('dashboard', ['tenant_id' => tenant('id')]);
    })->middleware(['auth'])->name('dashboard');

    // Your existing routes - they will now work within tenant context

    // Simple health check
    Route::get('/health', function () {
        return response('ok tenant:' . tenant('id'), 200);
    });

    // Lightweight SSO endpoint for landlord impersonation
    Route::get('/sso', function (\Illuminate\Http\Request $request) {
        $tenantId = $request->query('tenant');
        $email = $request->query('email');
        $expires = (int) $request->query('expires');
        $sig = $request->query('sig');

        if (! $tenantId || ! $email || ! $expires || ! $sig) {
            Log::warning('SSO invalid payload', compact('tenantId','email','expires'));
            abort(403, 'Invalid SSO payload.');
        }

        if ($tenantId !== tenant('id')) {
            Log::warning('SSO tenant mismatch', ['expected' => tenant('id'), 'got' => $tenantId]);
            abort(403, 'Tenant mismatch.');
        }

        if ($expires < now()->timestamp) {
            Log::warning('SSO expired', ['expires' => $expires, 'now' => now()->timestamp]);
            abort(403, 'SSO link expired.');
        }

        $key = config('app.key');
        if (is_string($key) && str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        $payload = $tenantId . '|' . $email . '|' . $expires;
        $expected = hash_hmac('sha256', $payload, $key ?? '');

        if (! hash_equals($expected, $sig)) {
            Log::warning('SSO bad signature');
            abort(403, 'Invalid SSO signature.');
        }

        $user = \App\Models\User::where('email', $email)->first();
        if (! $user) {
            Log::warning('SSO user not found', ['email' => $email]);
            abort(404, 'Owner user not found in tenant.');
        }

        Auth::login($user);
        Log::info('SSO login success', ['email' => $email]);
        return redirect()->route('dashboard');
    })->name('tenant.sso');
    
    // Inventory Routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/admin/inventory/create', function () {
        return view('inventory.create');
    })->name('inventory.create');
    Route::post('/admin/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
    
    // Jobcard Routes
    Route::get('/jobcard', [JobcardController::class, 'index'])->name('jobcard.index');
    Route::post('/jobcard', [JobcardController::class, 'store'])->name('jobcard.store');
    Route::get('/jobcard/create', [JobcardController::class, 'create'])->name('jobcard.create');
    Route::get('/jobcard/{jobcard}', [JobcardController::class, 'show'])->name('jobcard.show');
    Route::get('/jobcard/{jobcard}/edit', [JobcardController::class, 'edit'])->name('jobcard.edit');
    Route::put('/jobcard/{jobcard}', [JobcardController::class, 'update'])->name('jobcard.update');
    
    // Customer Routes
    Route::resource('customers', CustomerController::class);
    
    // Employee Routes
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    
    // Mobile Routes
    Route::get('/mobile/jobcards', [JobcardController::class, 'mobileIndex'])->name('mobile.jobcards.index');
    Route::post('/mobile-app/login', [MobileAuthController::class, 'login'])->name('mobile.login');
    
    // Add all your other existing routes here...
});
