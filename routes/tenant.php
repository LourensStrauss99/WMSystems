<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Import all the application controllers
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobcardController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MasterSettingsController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceivedVoucherController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrvController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\MobileJobcardPhotoController;
use App\Http\Controllers\MobileAuthController;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Volt;


// Debug route for domain testing
Route::get('/debug-domain', function () {
    return response()->json([
        'request_host' => request()->getHost(),
        'request_url' => request()->url(),
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'not set',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'not set',
        'tenant_id' => tenant('id'),
        'database' => \Illuminate\Support\Facades\DB::getDatabaseName(),
        'all_headers' => request()->headers->all(),
    ]);
});

// All tenant routes with proper tenancy middleware
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    Route::get('/settings/debug', [\App\Http\Controllers\MasterSettingsController::class, 'debug'])->name('settings.debug');
    // Authentication Routes
    Auth::routes(['verify' => true]);

    // Home/Dashboard Routes
    Route::get('/', function () {
        if (Auth::check()) {
            return view('dashboard', ['tenant_id' => tenant('id')]);
        }
        return view('auth.login');
    })->name('home');

    Route::get('/dashboard', function () {
        return view('dashboard', ['tenant_id' => tenant('id')]);
    })->middleware(['auth'])->name('dashboard');

    // Simple health check
    Route::get('/health', function () {
        return response('ok tenant:' . tenant('id'), 200);
    });

    // SSO endpoint for landlord impersonation
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

    // Profile Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [UserController::class, 'profile'])->name('profile.show');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
    });

    // Inventory Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::get('/admin/inventory/create', function () {
            return view('inventory.create');
        })->name('admin.inventory.create');
        Route::post('/admin/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
    });

    // Jobcard Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/jobcard', [JobcardController::class, 'index'])->name('jobcard.index');
        Route::get('/jobcard/create', [JobcardController::class, 'create'])->name('jobcard.create');
        Route::post('/jobcard', [JobcardController::class, 'store'])->name('jobcard.store');
        Route::get('/jobcard/{jobcard}', [JobcardController::class, 'show'])->name('jobcard.show');
        Route::get('/jobcard/{jobcard}/edit', [JobcardController::class, 'edit'])->name('jobcard.edit');
        Route::put('/jobcard/{jobcard}', [JobcardController::class, 'update'])->name('jobcard.update');
        Route::delete('/jobcard/{jobcard}', [JobcardController::class, 'destroy'])->name('jobcard.destroy');
        
        // Jobcard additional actions
        Route::post('/jobcard/{jobcard}/assign-employee', [JobcardController::class, 'assignEmployee'])->name('jobcard.assign-employee');
        Route::post('/jobcard/{jobcard}/add-inventory', [JobcardController::class, 'addInventory'])->name('jobcard.add-inventory');
        Route::get('/api/jobcards/{jobcard}', [JobcardController::class, 'getJobcard']);
        Route::put('/api/jobcards/{jobcard}', [JobcardController::class, 'updateJobcard']);
        Route::get('/jobcard/{jobcard}/pdf', [JobcardController::class, 'generatePdf'])->name('jobcard.pdf');
        Route::get('/api/jobcards', [JobcardController::class, 'apiIndex']);
    });

    // Customer Routes
    Route::middleware(['auth'])->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::patch('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
        Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
        Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
        Route::view('/client', 'client')->name('client');
        Route::get('/client/create', [CustomerController::class, 'create'])->name('client.create');
        Route::post('/client', [CustomerController::class, 'store'])->name('client.store');
        Route::get('/client/{client}', [CustomerController::class, 'show'])->name('client.show');
        Route::get('/client/{client}/edit', [CustomerController::class, 'edit'])->name('client.edit');
        Route::put('/client/{client}', [CustomerController::class, 'update'])->name('client.update');
        Route::delete('/client/{client}', [CustomerController::class, 'destroy'])->name('client.destroy');
    });

    // Employee Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        
        // Employee jobcard routes
        Route::get('/employees/{employee}/jobcards', [EmployeeController::class, 'jobcards'])->name('employees.jobcards');
        Route::post('/employees/{employee}/assign-jobcard', [EmployeeController::class, 'assignJobcard'])->name('employees.assign-jobcard');
    });

    // Invoice Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
        Route::post('/invoice', [InvoiceController::class, 'store'])->name('invoice.store');
        Route::get('/invoice/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
        Route::get('/invoice/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
        Route::put('/invoice/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');
        Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
        Route::get('/invoice/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoice.pdf');
        
        // Invoice API routes
        Route::get('/api/invoices', [InvoiceController::class, 'apiIndex']);
        Route::get('/api/invoices/{invoice}', [InvoiceController::class, 'apiShow']);
    });

    // Payment Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // Progress Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
        Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/progress/create', [ProgressController::class, 'create'])->name('progress.create');
    Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');
    Route::get('/progress/{progress}', [ProgressController::class, 'show'])->name('progress.show');
    Route::get('/progress/{progress}/edit', [ProgressController::class, 'edit'])->name('progress.edit');
    Route::put('/progress/{progress}', [ProgressController::class, 'update'])->name('progress.update');
    // Show jobcard progress
    Route::get('/progress/jobcard/{jobcard}', [ProgressController::class, 'showJobcardProgress'])->name('progress.jobcard.show');
        Route::delete('/progress/{progress}', [ProgressController::class, 'destroy'])->name('progress.destroy');
        
        // Progress API routes
        Route::get('/api/progress', [ProgressController::class, 'apiIndex']);
        Route::post('/api/progress', [ProgressController::class, 'apiStore']);
        Route::put('/api/progress/{progress}', [ProgressController::class, 'apiUpdate']);
    });

    // Purchase Order Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        
        // Approvals dashboard
        Route::get('/approvals', [PurchaseOrderController::class, 'approvals'])->name('approvals.index');
        
        Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::get('/purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
        Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
        Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
        Route::get('/purchase-orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'generatePdf'])->name('purchase-orders.pdf');
    });

    // Supplier Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // GRV (Goods Received Voucher) Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/grv', [GrvController::class, 'index'])->name('grv.index');
        Route::get('/grv/create', [GrvController::class, 'create'])->name('grv.create');
        Route::post('/grv', [GrvController::class, 'store'])->name('grv.store');
        Route::get('/grv/{grv}', [GrvController::class, 'show'])->name('grv.show');
        Route::get('/grv/{grv}/edit', [GrvController::class, 'edit'])->name('grv.edit');
        Route::put('/grv/{grv}', [GrvController::class, 'update'])->name('grv.update');
        Route::delete('/grv/{grv}', [GrvController::class, 'destroy'])->name('grv.destroy');
        
        // Alternative route naming
        Route::get('/goods-received-vouchers', [GoodsReceivedVoucherController::class, 'index'])->name('goods-received-vouchers.index');
        Route::get('/goods-received-vouchers/create', [GoodsReceivedVoucherController::class, 'create'])->name('goods-received-vouchers.create');
        Route::post('/goods-received-vouchers', [GoodsReceivedVoucherController::class, 'store'])->name('goods-received-vouchers.store');
        Route::get('/goods-received-vouchers/{goodsReceivedVoucher}', [GoodsReceivedVoucherController::class, 'show'])->name('goods-received-vouchers.show');
        Route::get('/goods-received-vouchers/{goodsReceivedVoucher}/edit', [GoodsReceivedVoucherController::class, 'edit'])->name('goods-received-vouchers.edit');
        Route::put('/goods-received-vouchers/{goodsReceivedVoucher}', [GoodsReceivedVoucherController::class, 'update'])->name('goods-received-vouchers.update');
        Route::delete('/goods-received-vouchers/{goodsReceivedVoucher}', [GoodsReceivedVoucherController::class, 'destroy'])->name('goods-received-vouchers.destroy');
    });

    // Company/Settings Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
        Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
        Route::put('/company', [CompanyController::class, 'update'])->name('company.update');
        Route::get('/company/details', [CompanyController::class, 'show'])->name('company.details');
        Route::put('/company/details', [CompanyController::class, 'update'])->name('company.details.update');
        Route::post('/company/remove-logo', [CompanyController::class, 'removeLogo'])->name('company.remove-logo');
        
        Route::get('/settings', [MasterSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [MasterSettingsController::class, 'store'])->name('settings.store');
        Route::put('/settings/{setting}', [MasterSettingsController::class, 'update'])->name('settings.update');

        // Support legacy central URLs under tenant domain
        Route::get('/master-settings', [MasterSettingsController::class, 'index'])->name('master.settings');
        Route::post('/master-settings', [MasterSettingsController::class, 'store'])->name('master.settings.store');
        Route::put('/master-settings/{setting}', [MasterSettingsController::class, 'update'])->name('master.settings.update');
    });

    // Report Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/jobcards', [ReportController::class, 'jobcards'])->name('reports.jobcards');
        Route::get('/reports/employees', [ReportController::class, 'employees'])->name('reports.employees');
    });

    // User Management Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Mobile App Routes
    Route::prefix('mobile')->name('mobile.')->group(function () {
        // Mobile Authentication
        Route::post('/login', [MobileAuthController::class, 'login'])->name('login');
        Route::post('/logout', [MobileAuthController::class, 'logout'])->name('logout');
        
        // Mobile Jobcards
        Route::middleware([\App\Http\Middleware\EmployeeAuth::class])->group(function () {
            Route::get('/jobcards', [JobcardController::class, 'mobileIndex'])->name('jobcards.index');
            Route::get('/jobcards/{jobcard}', [JobcardController::class, 'mobileShow'])->name('jobcards.show');
            Route::put('/jobcards/{jobcard}', [JobcardController::class, 'mobileUpdate'])->name('jobcards.update');
            
            // Mobile Photo Upload
            Route::post('/jobcards/{jobcard}/photos', [MobileJobcardPhotoController::class, 'store'])->name('jobcards.photos.store');
            Route::delete('/photos/{photo}', [MobileJobcardPhotoController::class, 'destroy'])->name('photos.destroy');
        });
    });

    // Phone/SMS Routes
    Route::middleware(['auth'])->group(function () {
        Route::post('/verify-phone', [PhoneController::class, 'verify'])->name('phone.verify');
        Route::post('/send-verification', [PhoneController::class, 'sendVerification'])->name('phone.send-verification');
    });

    // Email Verification Routes
    Route::get('/email/verify', [VerificationController::class, 'show'])
        ->middleware('auth')
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['auth', 'signed'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

    // Admin Panel Routes (within tenant context)
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    });

    // Livewire routes (within tenant context)
    Volt::route('/add-client-modal', 'add-client-modal');
    Volt::route('/client-form', 'client-form');
    Volt::route('/customer-search', 'customer-search');
    Volt::route('/customers-table', 'customers-table');
    Volt::route('/jobcard-editor', 'jobcard-editor');
    Volt::route('/jobcard-form', 'jobcard-form');
    Volt::route('/test-component', 'test-component');

    // Raw DB insert test for tenant connection
    Route::get('/settings/test-raw-insert', function () {
        $data = [
            'name' => 'RawTest',
            'surname' => 'RawTest',
            'email' => 'rawtest' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'tester',
            'admin_level' => 0,
            'is_superuser' => false,
            'employee_id' => 'RAW' . uniqid(),
            'department' => 'Testing',
            'position' => 'Raw Insert',
            'telephone' => '000',
            'is_active' => true,
            'created_by' => 1,
        ];
        $id = DB::connection('tenant')->table('employees')->insertGetId($data);
        return response()->json([
            'inserted_id' => $id,
            'connection' => 'tenant',
            'database' => DB::connection('tenant')->getDatabaseName(),
        ]);
    });

});
