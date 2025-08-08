<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Livewire\Volt\Volt;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;

// Debug route for domain testing
Route::get('/debug-domain', function () {
    return response()->json([
        'request_host' => request()->getHost(),
        'request_url' => request()->url(),
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'not set',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'not set',
        'all_headers' => request()->headers->all(),
    ]);
});
use App\Http\Controllers\JobcardController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MasterSettingsController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\QuotesController;
use App\Http\Controllers\ReportController;
use App\Models\Tenant;

// ========================================
// EXISTING ROUTES CONTINUE BELOW
// ========================================
// use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceivedVoucherController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrvController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;

use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\JobcardForm;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\MobileJobcardPhotoController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\ClientStatementController;
use Illuminate\Support\Facades\Log;

// Tenant Registration Routes (Public - no auth required)
Route::get('/tenant/register', [\App\Http\Controllers\TenantController::class, 'showRegistration'])->name('tenant.show-registration');
Route::post('/tenant/register', [\App\Http\Controllers\TenantController::class, 'register'])->name('tenant.register');

// Authentication Routes
Auth::routes(['verify' => true]);

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/email/resend', [VerificationController::class, 'resendEmail'])->name('verification.send');
    Route::post('/phone/send-code', [VerificationController::class, 'sendPhoneCode'])->name('verification.phone.send');
    Route::post('/phone/verify', [VerificationController::class, 'verifyPhone'])->name('verification.phone.verify');
    
    // Testing bypass (local only)
    Route::get('/verification/bypass', [VerificationController::class, 'bypassVerification'])->name('verification.bypass');
});

// Home Route - redirect to login
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Dashboard Route (Protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Landlord routes (restricted to admin_level 10 + is_landlord = 1)
Route::middleware(['auth', 'landlord'])->group(function () {
    Route::get('/landlord/tenants', function () {
        $tenants = \App\Models\Tenant::with('domains')->get();
        return view('landlord.tenants', compact('tenants'));
    })->name('landlord.tenants.index');
    
    Route::post('/landlord/tenants', function () {
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'tenant_id' => 'required|string|max:255|unique:tenants,id',
            'domain' => 'nullable|string|max:255|unique:domains,domain',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_password' => 'required|string|min:8',
            'owner_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'monthly_fee' => 'nullable|numeric|min:0',
            'subscription_plan' => 'nullable|string|in:basic,standard,premium,enterprise',
        ]);
        
        // Generate slug and database name
        $data['slug'] = \App\Models\Tenant::generateSlug($data['name']);
        $data['database_name'] = 'tenant_' . $data['tenant_id'];
    $data['status'] = 'active';
    $data['is_active'] = true;
    $data['payment_status'] = 'active';
        
        // Set default domain if not provided
        if (empty($data['domain'])) {
            $data['domain'] = $data['tenant_id'] . '.workflow-management.test';
        } else {
            // Normalize: accept bare label and expand to FQDN if needed
            if (strpos($data['domain'], '.') === false) {
                $data['domain'] = strtolower($data['domain']) . '.workflow-management.test';
            }
        }
        
        // Prevent duplicate domains (handles when domain was blank and defaulted)
        if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $data['domain'])->exists()) {
            return back()->withErrors(['domain' => 'This domain is already in use.'])->withInput();
        }

        // Create tenant
        $tenant = \App\Models\Tenant::create([
            'id' => $data['tenant_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'database_name' => $data['database_name'],
            'owner_name' => $data['owner_name'],
            'owner_email' => $data['owner_email'],
            'owner_password' => $data['owner_password'], // Will be hashed by model
            'owner_phone' => $data['owner_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? 'South Africa',
            'status' => $data['status'],
            'is_active' => $data['is_active'],
            'payment_status' => $data['payment_status'],
            'monthly_fee' => $data['monthly_fee'] ?? 0.00,
            'subscription_plan' => $data['subscription_plan'] ?? 'basic',
        ]);
        
        // Create domain
        $tenant->domains()->create(['domain' => $data['domain']]);

        // Initialize tenancy and bootstrap the owner inside the tenant DB
        try {
            tenancy()->initialize($tenant);

            // Create initial owner as super admin level 5
            \App\Models\User::query()->updateOrCreate(
                ['email' => $data['owner_email']],
                [
                    'name' => $data['owner_name'],
                    'password' => bcrypt($data['owner_password']),
                    'role' => 'admin',
                    'admin_level' => 5,
                    'is_superuser' => 1,
                    'is_active' => 1,
                    'email_verified_at' => now(),
                ]
            );

        } catch (\Throwable $e) {
            // Don't fail the whole flow; log and continue
            Log::error('Tenant bootstrap failed: ' . $e->getMessage());
        } finally {
            tenancy()->end();
        }

        // Optional: record initial payment in CENTRAL DB if monthly_fee provided
        if (!empty($data['monthly_fee'])) {
            DB::table('tenant_payments')->insert([
                'tenant_id' => $tenant->id,
                'amount' => $data['monthly_fee'],
                'method' => 'manual',
                'status' => 'paid',
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('landlord.tenants.index')
            ->with('success', "Tenant '{$data['name']}' created. Domain: {$data['domain']}. Owner provisioned as super admin.");
    })->name('landlord.tenants.store');

    // View tenant payment history (central DB)
    Route::get('/landlord/tenants/{tenant}/payments', function (\App\Models\Tenant $tenant) {
        $payments = DB::table('tenant_payments')
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->get();

        return view('landlord.tenant_payments', compact('tenant', 'payments'));
    })->name('landlord.tenants.payments');

    // Impersonate owner: redirect to tenant domain with a short-lived signed SSO token
    Route::get('/landlord/tenants/{tenant}/impersonate', function (\App\Models\Tenant $tenant) {
        $domain = optional($tenant->domains()->first())->domain;
        if (!$domain) {
            return back()->withErrors(['domain' => 'No domain configured for this tenant.']);
        }

        $email = $tenant->owner_email;
        $expires = now()->addMinutes(5)->timestamp;
        $payload = $tenant->id . '|' . $email . '|' . $expires;
        $key = config('app.key');
        if (is_string($key) && str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        $sig = hash_hmac('sha256', $payload, $key ?? '');

        $query = http_build_query([
            'tenant' => $tenant->id,
            'email' => $email,
            'expires' => $expires,
            'sig' => $sig,
        ]);

        return redirect("http://{$domain}/sso?{$query}");
    })->name('landlord.tenants.impersonate');

    // Diagnostics: check tenant initialization and DB connectivity
    Route::get('/landlord/tenants/{tenant}/check', function (\App\Models\Tenant $tenant) {
        $out = [ 'tenant_id' => $tenant->id ];
        try {
            tenancy()->initialize($tenant);
            $out['tenancy'] = 'initialized';
            // Simple DB smoke test on tenant connection
            $out['db_name'] = DB::getDatabaseName();
            $out['users_count'] = \App\Models\User::count();
            return response()->json($out);
        } catch (\Throwable $e) {
            $out['error'] = $e->getMessage();
            $out['trace_top'] = collect(explode("\n", $e->getTraceAsString()))->take(5);
            return response()->json($out, 500);
        } finally {
            tenancy()->end();
        }
    })->name('landlord.tenants.check');
});

// Settings (protected, using Volt)
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Inventory
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/admin/inventory/create', function () {
    return view('inventory.create');
})->name('inventory.create');
Route::post('/admin/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
Route::post('/inventory/check-stock', [InventoryController::class, 'checkStock'])->name('inventory.check-stock');
Route::get('/inventory/stock-alerts', [InventoryController::class, 'getLowStockAlerts']);
Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');

// Inventory API endpoints for code generation and markup
Route::get('/api/inventory/generate-code/{departmentPrefix}', [InventoryController::class, 'generateCode']);
Route::get('/api/inventory/search-for-po', [InventoryController::class, 'searchForPO']);
Route::get('/api/company/markup-percentage', [InventoryController::class, 'getCompanyMarkup']);
Route::get('/api/inventory/{id}/details', [InventoryController::class, 'getItemDetails']);

// Admin panel
Route::get('/admin-panel', [InventoryController::class, 'adminPanel'])->name('admin.panel');

// User management
Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');

// Static pages
Route::view('/client', 'client')->name('client');
Route::view('/invoice', 'invoice')->name('invoice');
Route::view('/settings', 'settings')->name('settings');
//Route::view('/reports', 'reports')->name('reports');
Route::view('/progress', 'progress')->name('progress');
Route::view('/artisanprogress', 'artisanprogress')->name('artisanprogress');

// Jobcard resource (RESTful) - exclude destroy method
Route::resource('jobcard', JobcardController::class)->except(['destroy']);
//Route::view('/jobcard', 'jobcard')->name('jobcard.index');
//Route::get('/jobcard/create/{client}', [JobcardController::class, 'create'])->name('jobcard.create');
Route::post('/jobcard', [JobcardController::class, 'store'])->name('jobcard.store');
Route::get('/jobcard/{jobcard}', [JobcardController::class, 'show'])->name('jobcard.show');
Route::post('/jobcard/{id}/submit-invoice', [JobcardController::class, 'submitForInvoice'])->name('jobcard.submitInvoice');
Route::post('/jobcard/{id}/remove-from-mobile', [JobcardController::class, 'removeFromMobile'])->name('jobcard.removeFromMobile');

// Home after login (default Laravel redirect)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home.index');

// Redirect old .html route if needed
Route::get('/Inventory.html', function () {
    return redirect('/inventory');
});

// Admin Authentication Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Master Settings
Route::get('/master-settings', [MasterSettingsController::class, 'index'])
    ->middleware(['auth'])
    ->name('master.settings');
Route::put('/master-settings/update', [MasterSettingsController::class, 'update'])->name('master.settings.update');
Route::get('/progress', [JobcardController::class, 'progress'])->name('progress');

Route::put('/progress/jobcard/{id}', [ProgressController::class, 'updateProgress'])->name('progress.jobcard.update');
Route::get('/invoice/{jobcard}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoice.show');
Route::get('/invoice', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice.index');
Route::get('/invoices/{jobcard}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
Route::post('/invoice/{jobcard}/email', [App\Http\Controllers\InvoiceController::class, 'email'])->name('invoice.email');
Route::get('/progress/assigned', [ProgressController::class, 'assignedAjax']);
Route::get('/progress/inprogress', [ProgressController::class, 'inprogressAjax']);
Route::get('/progress/completed', [ProgressController::class, 'completedAjax']);



Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/client/create', [CustomerController::class, 'create'])->name('client.create');
Route::post('/client/create', [CustomerController::class, 'store'])->name('client.store');
Route::get('/client/{id}', [CustomerController::class, 'show'])->name('client.show');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('client.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('client.store');
Route::get('/client/{id}/edit', [CustomerController::class, 'edit'])->name('client.edit');
Route::put('/client/{id}', [CustomerController::class, 'update'])->name('client.update');
Route::post('/client/{id}/notes', [CustomerController::class, 'updateNotes'])->name('client.notes');
Route::post('/client/{id}/regenerate-reference', [CustomerController::class, 'regenerateReference'])->name('client.regenerate-reference');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

// TEMPORARY: No middleware for testing
// Only superuser (level 4) user can access company details
//Route::middleware(['auth', 'admin'])->group(function () {
  //  Route::get('/company-details', function () {
   //     return view('company-details');
   // })->name('company.details');
  //  Route::put('/company-details', [MasterSettingsController::class, 'updateCompanyDetails'])->name('company.details.update');
//});
//Route::get('/company-details', function () {
//    return view('company-details');
//})->name('company.details');
//Route::put('/company-details', [MasterSettingsController::class, 'updateCompanyDetails'])->name('company.details.update');
Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');
// Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
Route::get('/progress/jobcard/{id}', [ProgressController::class, 'show'])->name('progress.jobcard.show');

// Add this temporary route at the end:
Route::get('/fix-susan-reference', function() {
    // First, let's add the column if it doesn't exist
    try {
        if (!Schema::hasColumn('clients', 'payment_reference')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('payment_reference', 8)->nullable()->unique();
            });
        }
    } catch (Exception $e) {
        // Column might already exist
    }
    
    // Generate reference for Susan
    $reference = 'STR' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    
    DB::table('clients')->where('id', 5)->update([
        'payment_reference' => $reference
    ]);
    
    return "Susan's payment reference updated to: " . $reference;
});

// Add these payment routes:

Route::middleware(['auth'])->group(function () {
    Route::get('/client/{client}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::post('/payments/lookup-invoice', [PaymentController::class, 'getInvoiceDetails'])->name('payments.lookup');
    
    // Customer management routes
    Route::get('/client/{client}/edit', [CustomerController::class, 'edit'])->name('clients.edit');
    Route::put('/client/{client}', [CustomerController::class, 'update'])->name('clients.update');
    Route::post('/client/{client}/notes', [CustomerController::class, 'updateNotes'])->name('clients.notes');
    Route::post('/client/{client}/regenerate-reference', [CustomerController::class, 'regenerateReference'])->name('clients.regenerate-reference');
}); 

Route::get('/invoice/{jobcard}/pdf', [InvoiceController::class, 'generatePDF'])->name('invoice.pdf');
Route::get('/jobcard/{jobcard}/pdf', [JobcardController::class, 'generatePDF'])->name('jobcard.pdf');

// Purchase Orders
Route::middleware(['auth'])->group(function () {
    
    // Purchase Orders - Clean up duplicates and fix parameter names
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::get('/purchase-orders/create/low-stock', [PurchaseOrderController::class, 'createFromLowStock'])->name('purchase-orders.create-low-stock');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    
    // Approvals dashboard
    Route::get('/approvals', [PurchaseOrderController::class, 'approvals'])->name('approvals.index');
    
    // SPECIFIC routes MUST come before generic {purchaseOrder} route
    Route::get('/purchase-orders/{id}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
    Route::get('/purchase-orders/{id}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::get('/purchase-orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'generatePDF'])->name('purchase-orders.pdf');
    Route::put('/purchase-orders/{id}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
    Route::delete('/purchase-orders/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
    Route::post('/purchase-orders/{id}/update-status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    
    // Approval workflow routes - FIXED parameter names
    Route::post('/purchase-orders/{purchaseOrder}/submit-for-approval', [PurchaseOrderController::class, 'submitForApproval'])->name('purchase-orders.submit-for-approval');
    Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])
        ->name('purchase-orders.reject');
    Route::post('/purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'sendToSupplier'])->name('purchase-orders.send');
    
    // Generic show route - MUST come LAST
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    
    // Suppliers and GRV routes
    Route::resource('suppliers', SupplierController::class);
    Route::resource('grv', GrvController::class)->names([
        'index' => 'grv.index',
        'create' => 'grv.create',
        'store' => 'grv.store',
        'show' => 'grv.show',
        'edit' => 'grv.edit',
        'update' => 'grv.update',
        'destroy' => 'grv.destroy',
    ]);
});

// Company Management Routes
Route::middleware(['auth'])->group(function () {
    // Company routes
    Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company/update', [CompanyController::class, 'update'])->name('company.update');
    Route::get('/company/details', [CompanyController::class, 'show'])->name('company.details');
    Route::post('/company/remove-logo', [CompanyController::class, 'removeLogo'])->name('company.remove-logo');
});

// Add this after your existing routes for debugging

Route::post('debug-po', function(\Illuminate\Http\Request $request) {
    dd([
        'all_data' => $request->all(),
        'has_items' => $request->has('items'),
        'items_data' => $request->input('items'),
        'method' => $request->method(),
        'url' => $request->url()
    ]);
})->name('debug.po');

// Debug routes (add at the very end)
Route::get('/debug-approvals', function() {
    try {
        $allPOs = \App\Models\PurchaseOrder::all();
        $pendingOrders = \App\Models\PurchaseOrder::where('status', 'pending_approval')->get();
        
        return response()->json([
            'total_pos' => $allPOs->count(),
            'all_statuses' => $allPOs->pluck('status', 'id')->toArray(),
            'pending_count' => $pendingOrders->count(),
            'pending_orders' => $pendingOrders->toArray(),
            'latest_po' => $allPOs->latest()->first()?->toArray(),
            'fillable_fields' => (new \App\Models\PurchaseOrder())->getFillable(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/force-pending', function() {
    try {
        $latestPO = \App\Models\PurchaseOrder::latest()->first();
        if ($latestPO) {
            // Force update using DB query
            DB::table('purchase_orders')
                ->where('id', $latestPO->id)
                ->update([
                    'status' => 'pending_approval',
                    'submitted_for_approval_at' => now(),
                    'submitted_by' => optional(auth())->id()
                ]);
            return "Forced PO {$latestPO->id} to pending_approval status. Check /debug-approvals now.";
        }
        return "No PO found";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage(); 
    }
});

// Add this route to check database
Route::get('/check-db', function() {
    try {
        // Check if table exists
        $tableExists = \Illuminate\Support\Facades\Schema::hasTable('purchase_orders');
        
        // Check columns
        $columns = [];
        if ($tableExists) {
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('purchase_orders');
        }
        
        return response()->json([
            'table_exists' => $tableExists,
            'columns' => $columns,
            'has_status_column' => in_array('status', $columns),
            'sample_data' => $tableExists ? \App\Models\PurchaseOrder::first()?->toArray() : null
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Add these routes to your existing routes section:
Route::middleware(['auth'])->group(function () {
    // ... existing routes ...
    
    // GRV routes
    Route::resource('grv', GrvController::class);
    Route::post('/grv/{id}/approve', [GrvController::class, 'approve'])->name('grv.approve');
    Route::post('/grv/{id}/quality-pass', [GrvController::class, 'passQualityCheck'])->name('grv.quality-pass');
    Route::post('/grv/{id}/quality-fail', [GrvController::class, 'failQualityCheck'])->name('grv.quality-fail');
    Route::get('/api/purchase-orders/{id}/details', [GrvController::class, 'getPurchaseOrderDetails'])->name('api.purchase-orders.details');
});

// Add these routes to your web.php
//Route::middleware(['auth'])->group(function () {
//    Route::get('/company-details', [CompanyController::class, 'edit'])->name('company.details');
//    Route::put('/company-details', [CompanyController::class, 'update'])->name('company.details.update');
//    Route::get('/company/remove-logo', [CompanyController::class, 'removeLogo'])->name('company.remove-logo');
//    Route::get('/company/check-setup', [CompanyController::class, 'checkSetup'])->name('company.check-setup');
//});

// Add to your routes/web.php:

Route::middleware(['auth'])->group(function () {
    //Route::get('/master_settings', [MasterSettingsController::class, 'index'])->name('master.settings');
    
    // User management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Employee management routes - MOVED INSIDE MIDDLEWARE
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    
    // Other routes...
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/company/check-setup', [CompanyController::class, 'checkSetup'])->name('company.check-setup');
    Route::get('/company/details', [CompanyController::class, 'edit'])->name('company.details');
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
});

// Employee management routes
Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');

// Employee management routes
Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

// Customer resourceful routes
Route::resource('customers', CustomerController::class);
Route::patch('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

// GRV debug and force-update
Route::get('/grv/{id}/debug', [GrvController::class, 'debugApproval'])->name('grv.debug');
Route::get('/grv/{id}/force-update', [GrvController::class, 'forceUpdateInventory'])->name('grv.force-update');

// Inventory test update
Route::get('/test-inventory-update', function() {
    $inventory = App\Models\Inventory::find(1);
    if ($inventory) {
        $oldStock = $inventory->stock_level;
        $inventory->stock_level += 10;
        $saved = $inventory->save();
        return response()->json([
            'inventory_id' => $inventory->id,
            'old_stock' => $oldStock,
            'new_stock' => $inventory->fresh()->stock_level,
            'save_result' => $saved ? 'SUCCESS' : 'FAILED',
            'inventory_data' => $inventory->toArray()
        ]);
    }
    return response()->json(['error' => 'Inventory not found']);
});

// In web.php
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::post('/master_settings', [MasterSettingsController::class, 'store'])->name('master_settings.store');

// Add this to your web.php routes file
Route::post('/jobcard/calculate-hours', [JobcardController::class, 'calculateHourCosts'])->name('jobcard.calculate-hours');

// Add this route to your api.php or web.php
Route::get('/api/company-rates', function() {
    $company = \App\Models\CompanyDetail::first();
    
    if (!$company) {
        // Return default rates if no company details found
        return response()->json([
            'labour_rate' => 750.00,
            'overtime_multiplier' => 1.50,
            'weekend_multiplier' => 2.00,
            'public_holiday_multiplier' => 2.50,
            'call_out_rate' => 1000.00,
            'mileage_rate' => 7.50,
        ]);
    }
    
    return response()->json([
        'labour_rate' => floatval($company->labour_rate ?? 750),
        'overtime_multiplier' => floatval($company->overtime_multiplier ?? 1.5),
        'weekend_multiplier' => floatval($company->weekend_multiplier ?? 2.0),
        'public_holiday_multiplier' => floatval($company->public_holiday_multiplier ?? 2.5),
        'call_out_rate' => floatval($company->call_out_rate ?? 1000),
        'mileage_rate' => floatval($company->mileage_rate ?? 7.5),
    ]);
});

// Remove duplicates and use clean routes:
Route::middleware(['auth'])->group(function () {
    Route::get('/grv', [GrvController::class, 'index'])->name('grv.index');
    Route::get('/grv/create', [GrvController::class, 'create'])->name('grv.create');
    Route::post('/grv', [GrvController::class, 'store'])->name('grv.store');
    //Route::get('/grv/{id}', [GrvController::class, 'show'])->name('grv.show');
    // Other GRV routes...
});

// Invoice reminder
Route::post('/invoice/{invoice}/reminder', [App\Http\Controllers\InvoiceController::class, 'sendReminder'])->name('invoice.reminder');

// Customer statement
Route::post('/customer/{customer}/statement', [App\Http\Controllers\CustomerController::class, 'sendStatement'])->name('customer.statement');
Route::get('/customer/{customer}/statement/download', [App\Http\Controllers\CustomerController::class, 'downloadStatement'])->name('customer.statement.download');

// Client statement (new feature)
Route::get('/clients/{client}/statement', [ClientStatementController::class, 'show'])->name('clients.statement');

Route::get('/mobile/jobcard/{id}/edit', [JobcardController::class, 'editMobile'])->name('jobcard.edit.mobile');

// Employee-specific jobcard routes
Route::post('/employee/jobcards', [JobcardController::class, 'apiAssignedJobcards']);
Route::get('/jobcard/{id}/view', [JobcardController::class, 'apiViewJobcard']);
Route::put('/jobcard/{id}/update', [JobcardController::class, 'apiUpdateJobcard']);
// Mobile jobcard index view (device-received jobcards)
Route::get('/mobile-app/jobcard/index', function() {
    return view('mobile app.index.mobile');
})->name('jobcard.mobile.index');

Route::middleware([\App\Http\Middleware\EmployeeAuth::class])->group(function () {
    Route::get('/mobile/jobcards', [App\Http\Controllers\JobcardController::class, 'mobileIndex'])->name('mobile.jobcards.index');
    Route::get('/mobile/jobcards/create', [App\Http\Controllers\JobcardController::class, 'createMobile'])->name('mobile-jobcard.create');
    Route::get('/mobile/jobcards/{jobcard}/edit', [App\Http\Controllers\JobcardController::class, 'editMobile'])->name('mobile.jobcards.edit');
    Route::get('/mobile/jobcards/{jobcard}', [App\Http\Controllers\JobcardController::class, 'showMobile'])->name('mobile.jobcards.show');
    Route::post('/mobile/jobcards', [App\Http\Controllers\JobcardController::class, 'store'])->name('mobile-jobcard.store');
});

Route::post('/mobile-jobcard-photos', [MobileJobcardPhotoController::class, 'store'])->name('mobile-jobcard-photos.store');
Route::delete('/mobile-jobcard-photos/{id}', [MobileJobcardPhotoController::class, 'destroy'])->name('mobile-jobcard-photos.destroy');

Route::post('/mobile-app/login', [MobileAuthController::class, 'login'])->name('mobile.login');
Route::get('/mobile-app/login', fn () => view('mobile.login'))->name('mobile.login.form');

Route::post('/jobcard/{jobcard}/accept-quote', [App\Http\Controllers\JobcardController::class, 'acceptQuote'])->name('jobcard.acceptQuote');
