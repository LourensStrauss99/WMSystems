<?php

namespace App\Http\Controllers;

use App\Models\LandlordInvoice;
use App\Models\LandlordPayment;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use App\Models\TenantCommunication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LandlordDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'landlord']);
    }

    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        // Revenue analytics
        $monthlyRevenue = LandlordPayment::where('status', 'completed')
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $yearlyRevenue = LandlordPayment::where('status', 'completed')
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        // Package breakdown
        $packageRevenue = DB::table('tenants')
            ->join('landlord_payments', 'tenants.id', '=', 'landlord_payments.tenant_id')
            ->select('tenants.subscription_plan', DB::raw('SUM(landlord_payments.amount) as total_revenue'))
            ->where('landlord_payments.status', 'completed')
            ->whereYear('landlord_payments.payment_date', now()->year)
            ->groupBy('tenants.subscription_plan')
            ->get();

        // Outstanding invoices
        $outstandingAmount = LandlordInvoice::whereIn('status', ['pending', 'overdue'])->sum('total_amount');
        $overdueInvoices = LandlordInvoice::where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        // Tenant statistics
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $newTenantsThisMonth = Tenant::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Recent communications
        $recentCommunications = TenantCommunication::with(['tenant', 'initiatedBy'])
            ->latest()
            ->limit(5)
            ->get();

        return view('landlord.dashboard', compact(
            'monthlyRevenue',
            'yearlyRevenue',
            'packageRevenue',
            'outstandingAmount',
            'overdueInvoices',
            'totalTenants',
            'activeTenants',
            'newTenantsThisMonth',
            'recentCommunications'
        ));
    }

    public function income(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');

        // Monthly revenue chart data
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = LandlordPayment::where('status', 'completed')
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $i)
                ->sum('amount');
        }

        // Package revenue breakdown
        $packageBreakdown = DB::table('subscription_packages as sp')
            ->leftJoin('tenants as t', 't.subscription_plan', '=', 'sp.slug')
            ->leftJoin('landlord_payments as lp', function($join) use ($year, $month) {
                $join->on('t.id', '=', 'lp.tenant_id')
                     ->where('lp.status', '=', 'completed')
                     ->whereYear('lp.payment_date', '=', $year);
                if ($month) {
                    $join->whereMonth('lp.payment_date', '=', $month);
                }
            })
            ->select(
                'sp.name',
                'sp.slug',
                'sp.monthly_price',
                DB::raw('COUNT(DISTINCT t.id) as tenant_count'),
                DB::raw('COALESCE(SUM(lp.amount), 0) as total_revenue')
            )
            ->groupBy('sp.id', 'sp.name', 'sp.slug', 'sp.monthly_price')
            ->get();

        // Payment history
        $query = LandlordPayment::with('tenant')
            ->where('status', 'completed')
            ->whereYear('payment_date', $year);

        if ($month) {
            $query->whereMonth('payment_date', $month);
        }

        $payments = $query->latest('payment_date')->paginate(20);

        return view('landlord.income', compact(
            'monthlyData',
            'packageBreakdown',
            'payments',
            'year',
            'month'
        ));
    }

    public function tenants(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $package = $request->get('package');

        $query = Tenant::with(['landlordInvoices', 'landlordPayments', 'domains']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('owner_email', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'overdue') {
                $query->whereHas('landlordInvoices', function($q) {
                    $q->where('status', 'pending')
                      ->where('due_date', '<', now());
                });
            }
        }

        if ($package) {
            $query->where('subscription_plan', $package);
        }

        $tenants = $query->latest()->paginate(15);
        $packages = SubscriptionPackage::where('is_active', true)->get();

        return view('landlord.tenants.index', compact('tenants', 'packages'));
    }

    public function create()
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();
        return view('landlord.tenants.create', compact('packages'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['landlordInvoices', 'landlordPayments', 'communications.messages', 'domains']);
        
        // Get recent financial activity
        $recentInvoices = $tenant->landlordInvoices()
            ->latest()
            ->limit(5)
            ->get();
            
        $recentPayments = $tenant->landlordPayments()
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();
            
        // Calculate financial summary
        $totalInvoiced = $tenant->landlordInvoices()->sum('amount');
        $totalPaid = $tenant->landlordPayments()->where('status', 'completed')->sum('amount');
        $outstandingBalance = $tenant->landlordInvoices()->where('status', 'pending')->sum('amount');
        $overdueAmount = $tenant->landlordInvoices()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->sum('amount');
            
        return view('landlord.tenants.show', compact(
            'tenant', 'recentInvoices', 'recentPayments', 
            'totalInvoiced', 'totalPaid', 'outstandingBalance', 'overdueAmount'
        ));
    }

    public function edit(Tenant $tenant)
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();
        return view('landlord.tenants.edit', compact('tenant', 'packages'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'subscription_plan' => 'required|string',
            'monthly_fee' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'payment_status' => 'required|in:active,suspended,cancelled',
            'next_payment_due' => 'nullable|date'
        ]);

        $tenant->update($request->all());

        return redirect()->route('landlord.tenants.index')->with('success', 'Tenant updated successfully!');
    }

    public function store(Request $request)
    {
        // Get available package names for validation
        $availablePackages = SubscriptionPackage::where('is_active', true)->pluck('name')->toArray();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:domains,domain',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_password' => 'required|string|min:8',
            'owner_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'monthly_fee' => 'nullable|numeric|min:0',
            'subscription_plan' => 'required|string|in:' . implode(',', $availablePackages),
        ]);
        
        // Auto-generate unique tenant ID
        $data['tenant_id'] = $this->generateUniqueTenantId();
        
        // Generate slug and database name
        $data['slug'] = Tenant::generateSlug($data['name']);
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

        // Create tenant - this will trigger TenantCreated event which handles database creation and migration
        $tenant = Tenant::create([
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
            'subscription_plan' => $data['subscription_plan'],
        ]);
        
        // Create domain
        $tenant->domains()->create(['domain' => $data['domain']]);

        // Initialize tenancy and create ONLY the owner user (no additional data)
        try {
            tenancy()->initialize($tenant);

            // Create initial owner as super admin level 5 (ONLY user in tenant DB)
            \App\Models\User::create([
                'name' => $data['owner_name'],
                'email' => $data['owner_email'],
                'password' => bcrypt($data['owner_password']),
                'role' => 'admin',
                'admin_level' => 5,
                'is_superuser' => 1,
                'is_active' => 1,
                'email_verified_at' => now(),
            ]);

        } catch (\Throwable $e) {
            // If tenant setup fails, clean up and report error
            Log::error('Tenant owner creation failed: ' . $e->getMessage());
            
            // Clean up tenant if creation failed
            try {
                $tenant->delete();
            } catch (\Exception $cleanup_e) {
                Log::error('Failed to cleanup failed tenant: ' . $cleanup_e->getMessage());
            }
            
            return back()->withErrors(['tenant_creation' => 'Failed to create tenant owner: ' . $e->getMessage()])->withInput();
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
            ->with('success', "Tenant '{$data['name']}' created successfully! Tenant ID: {$data['tenant_id']}, Domain: {$data['domain']}. Owner provisioned as super admin.");
    }

    // Package Management Methods
    public function packages()
    {
        $packages = SubscriptionPackage::orderBy('sort_order')->orderBy('monthly_price')->get();
        return view('landlord.packages.index', compact('packages'));
    }

    public function createPackage()
    {
        return view('landlord.packages.create');
    }

    public function storePackage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'max_users' => 'required|integer|min:1',
            'storage_limit_mb' => 'required|integer|min:100',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        // Generate slug from name
        $data['slug'] = Str::slug($data['name']);
        
        // Ensure unique slug
        $originalSlug = $data['slug'];
        $counter = 1;
        while (SubscriptionPackage::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        SubscriptionPackage::create($data);

        return redirect()->route('landlord.packages.index')->with('success', 'Package created successfully!');
    }

    public function editPackage(SubscriptionPackage $package)
    {
        return view('landlord.packages.edit', compact('package'));
    }

    public function updatePackage(Request $request, SubscriptionPackage $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'max_users' => 'required|integer|min:1',
            'storage_limit_mb' => 'required|integer|min:100',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        // Update slug if name changed
        if ($package->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']);
            
            // Ensure unique slug
            $originalSlug = $data['slug'];
            $counter = 1;
            while (SubscriptionPackage::where('slug', $data['slug'])->where('id', '!=', $package->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $package->update($data);

        return redirect()->route('landlord.packages.index')->with('success', 'Package updated successfully!');
    }

    public function destroyPackage(SubscriptionPackage $package)
    {
        // Check if package is being used by any tenants
        $tenantsUsingPackage = Tenant::where('subscription_plan', $package->name)->count();
        
        if ($tenantsUsingPackage > 0) {
            return redirect()->route('landlord.packages.index')
                ->with('error', "Cannot delete package '{$package->name}' as it is currently being used by {$tenantsUsingPackage} tenant(s).");
        }

        $package->delete();

        return redirect()->route('landlord.packages.index')->with('success', 'Package deleted successfully!');
    }

    public function communications()
    {
        $communications = TenantCommunication::with(['tenant', 'initiatedBy', 'assignedTo', 'messages'])
            ->latest()
            ->paginate(20);

        return view('landlord.communications.index', compact('communications'));
    }

    /**
     * Generate a unique tenant ID
     */
    private function generateUniqueTenantId()
    {
        do {
            // Get the next incremental number based on existing tenants
            $lastTenant = Tenant::orderBy('created_at', 'desc')->first();
            
            if ($lastTenant) {
                // Get the highest ID and add 1
                $nextNumber = $lastTenant->id + 1;
            } else {
                // First tenant starts at 1
                $nextNumber = 1;
            }
            
            // Double-check uniqueness (in case of race conditions)
        } while (Tenant::where('id', $nextNumber)->exists());
        
        return $nextNumber;
    }

    public function createTenant()
    {
        return $this->create();
    }

    public function showTenant(Tenant $tenant)
    {
        return $this->show($tenant);
    }

    public function editTenant(Tenant $tenant)
    {
        return $this->edit($tenant);
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        return $this->update($request, $tenant);
    }

    public function destroyTenant(Tenant $tenant)
    {
        // Add tenant deletion logic here
        try {
            // Delete tenant domain records
            $tenant->domains()->delete();
            
            // Drop tenant database
            $databaseName = $tenant->database()->getName();
            DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
            
            // Delete tenant record
            $tenant->delete();
            
            return redirect()->route('landlord.tenants.index')
                ->with('success', 'Tenant deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting tenant: ' . $e->getMessage());
            return redirect()->route('landlord.tenants.index')
                ->with('error', 'Error deleting tenant: ' . $e->getMessage());
        }
    }

    public function showPackage(SubscriptionPackage $package)
    {
        $tenants = Tenant::where('subscription_plan', $package->name)->get();
        return view('landlord.packages.show', compact('package', 'tenants'));
    }

    public function ssoToTenant(Request $request, Tenant $tenant)
    {
        // Generate SSO token for tenant access
        $user = Auth::user();
        $expires = now()->addMinutes(5)->timestamp;
        
        $key = config('app.key');
        if (is_string($key) && str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        
        $payload = $tenant->id . '|' . $user->email . '|' . $expires;
        $signature = hash_hmac('sha256', $payload, $key ?? '');
        
        $ssoUrl = 'http://' . $tenant->domains()->first()->domain . '/sso?' . http_build_query([
            'tenant' => $tenant->id,
            'email' => $user->email,
            'expires' => $expires,
            'sig' => $signature,
        ]);
        
        return redirect($ssoUrl);
    }
}
