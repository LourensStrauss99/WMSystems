<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Livewire\Volt\Volt;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
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
// use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceivedVoucherController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrvController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\JobcardForm;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Authentication Routes
Auth::routes(['verify' => true]);

// Home Route
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Dashboard Route (Protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

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
Route::view('/quotes', 'quotes')->name('quotes');
Route::get('/quotes', [QuotesController::class, 'index'])->name('quotes.index');
Route::post('/quotes/save', [QuotesController::class, 'save'])->name('quotes.save');
Route::get('/quotes/{id}', [QuotesController::class, 'show'])->name('quotes.show');
Route::get('/quotes/{id}/download', [QuotesController::class, 'download'])->name('quotes.download');
Route::post('/quotes/{id}/email', [QuotesController::class, 'email'])->name('quotes.email');
//Route::view('/admin-panel', 'admin-panel')->name('admin-panel');
//Route::view('/admin/login', 'admin login')->name('admin.login');
//Route::view('/admin/register', 'admin.register')->name('admin.register');

// Jobcard resource (RESTful)
Route::resource('jobcard', JobcardController::class);
//Route::view('/jobcard', 'jobcard')->name('jobcard.index');
Route::get('/jobcard/create/{client}', [JobcardController::class, 'create'])->name('jobcard.create');
Route::post('/jobcard', [JobcardController::class, 'store'])->name('jobcard.store');
Route::get('/jobcard/{jobcard}', [JobcardController::class, 'show'])->name('jobcard.show');
Route::post('/jobcard/{id}/submit-invoice', [JobcardController::class, 'submitForInvoice'])->name('jobcard.submitInvoice');

// Home after login (default Laravel redirect)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

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
Route::get('/company-details', function () {
    return view('company-details');
})->name('company.details');
Route::put('/company-details', [MasterSettingsController::class, 'updateCompanyDetails'])->name('company.details.update');
Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');
// Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
Route::get('/progress/jobcard/{id}', [ProgressController::class, 'show'])->name('progress.jobcard.show');

// Add this temporary route at the end:

use App\Models\Client;

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
    Route::get('/purchase-orders/{id}/pdf', [PurchaseOrderController::class, 'generatePDF'])->name('purchase-orders.pdf');
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
Route::middleware(['auth'])->group(function () {
    Route::get('/company-details', [CompanyController::class, 'edit'])->name('company.details');
    Route::put('/company-details', [CompanyController::class, 'update'])->name('company.details.update');
    Route::get('/company/remove-logo', [CompanyController::class, 'removeLogo'])->name('company.remove-logo');
    Route::get('/company/check-setup', [CompanyController::class, 'checkSetup'])->name('company.check-setup');
});

// Add to your routes/web.php:

Route::middleware(['auth'])->group(function () {
    Route::get('/master_settings', [MasterSettingsController::class, 'index'])->name('master.settings');
    
    // User management routes - FIX THE STORE ROUTE
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store'); // Change from MasterSettingsController
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Add missing routes
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/company/check-setup', [CompanyController::class, 'checkSetup'])->name('company.check-setup');
    Route::get('/company/details', [CompanyController::class, 'edit'])->name('company.details');
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::get('/grv', [GrvController::class, 'index'])->name('grv.index');
});
