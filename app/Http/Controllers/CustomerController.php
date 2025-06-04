<?php
namespace App\Http\Controllers;

use App\Models\Client; // or Customer if your model is named Customer
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10); // default to 10

        // Only allow 10, 25, or 50
        if (!in_array($perPage, [10, 25, 50])) {
            $perPage = 10;
        }

        $customers = \App\Models\Client::query()
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends(['search' => $search, 'perPage' => $perPage]); // keep params in pagination

        return view('customers', [
            'customers' => $customers,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }
    
    public function show($id)
    {
        $customer = \App\Models\Client::with([
            'jobcards' => function($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // All jobcards for work history
        $workHistory = $customer->jobcards;

        // All invoices for this customer (from invoices table)
        $invoiceHistory = \App\Models\Invoice::where('client_id', $customer->id)
            ->orderBy('invoice_date', 'desc')
            ->get();

        return view('customer-show', compact('customer', 'workHistory', 'invoiceHistory'));
    }
    
    public function create()
    {
        return view('customer-create');
    }
    
    public function store(Request $request)
    {
        // Validate and save the customer
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);
        Client::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added!');
    }
}




// Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users.index');
// Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
// 
// // Jobcard management
// Route::get('/jobcards', [JobcardController::class, 'index'])->name('jobcards.index');
// Route::get('/jobcards/create', [JobcardController::class, 'create'])->name('jobcards.create');
// Route::post('/jobcards', [JobcardController::class, 'store'])->name('jobcards.store');
// 
// // Admin authentication
// Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
// Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
// 
// // Admin panel
// Route::get('/admin/panel', [AdminPanelController::class, 'index'])->name('admin.panel.index');
// 
// // Invoice management
// Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
// Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
// Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
// 
// // Customer management
// Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
// 
// // Master settings
// Route::get('/settings/master', [MasterSettingsController::class, 'index'])->name('settings.master.index');
// 
// // Progress tracking
// Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
// 
// // Phone management
// Route::get('/phones', [PhoneController::class, 'index'])->name('phones.index');
// 
// // Quotes management
// Route::get('/quotes', [QuotesController::class, 'index'])->name('quotes.index');