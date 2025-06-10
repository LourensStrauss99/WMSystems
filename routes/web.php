<?php

use Illuminate\Support\Facades\Route;
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
// use App\Http\Controllers\ProfileController;

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

// Admin panel


// User management
Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');

// Static pages (views)
Route::view('/client', 'client')->name('client');
Route::view('/invoice', 'invoice')->name('invoice');
Route::view('/settings', 'settings')->name('settings');
Route::view('/reports', 'reports')->name('reports');
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
Route::get('/jobcard/{jobcard}', [\App\Http\Controllers\JobcardController::class, 'show'])->name('jobcard.show');

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
Route::get('/progress', [App\Http\Controllers\JobcardController::class, 'progress'])->name('progress');
Route::get('/progress/jobcard/{id}', [JobcardController::class, 'showProgress'])->name('progress.jobcard.show');
Route::put('/progress/jobcard/{id}', [App\Http\Controllers\JobcardController::class, 'updateProgress'])->name('progress.jobcard.update');
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

