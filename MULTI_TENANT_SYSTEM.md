# Multi-Tenant Workflow Management System

## 🎯 Overview

Your workflow management system now has a complete multi-tenant architecture that creates isolated databases for each registered company. Here's how it works:

## 📋 System Components

### 1. **Main Application Database**

-   Contains the `tenants` table that stores all registered companies
-   Houses super admin users who can manage all tenants
-   Maintains system-wide configurations

### 2. **Tenant Databases**

-   Each company gets a completely isolated database
-   Named with pattern: `wms_{company_slug}_{date}_{counter}`
-   Example: `wms_test_company_ltd_20250806`
-   Contains all company-specific data (users, customers, jobcards, inventory, etc.)

### 3. **Authentication Flow**

-   **Tenant Registration**: Companies register via `/tenant/register` → redirects to login
-   **Login Process**: Users log in via `/login` → system automatically switches to correct tenant database
-   **Super Admin Access**: Super admins access tenant management via `/super-admin/dashboard`

## 🚀 How It Works

### **Company Registration Process**

1. Company visits `/tenant/register`
2. Fills out registration form with:
    - Company name, address, phone
    - Owner name, email, password
3. System creates:
    - Tenant record in main database
    - Isolated database for the company
    - Complete schema (users, customers, jobcards, inventory, etc.)
    - Super user with Level 5 admin rights in tenant database
4. Redirects to login page with success message

### **Login & Database Switching**

1. User logs in with email/password
2. `TenantMiddleware` finds which tenant database the user belongs to
3. Automatically switches database connection to tenant database
4. User operates in completely isolated environment
5. Super admins get redirected to super admin dashboard

### **Super Admin Management**

-   Super admins can view all tenants
-   Monitor tenant statistics
-   Suspend/activate tenants
-   Delete tenants and their databases
-   Login as any tenant for support purposes

## 📁 File Structure

```
app/
├── Http/Controllers/Tenant/
│   └── TenantController.php          # Handles tenant registration
├── Http/Controllers/
│   └── SuperAdminController.php      # Super admin tenant management
├── Http/Middleware/Tenant/
│   └── TenantMiddleware.php          # Auto-switches tenant databases
├── Models/
│   └── Tenant.php                    # Tenant model
├── Console/Commands/
│   ├── CreateTestTenant.php          # Testing command
│   └── ListTenants.php               # List all tenants
└── ...

resources/views/
├── tenant/
│   └── register.blade.php            # Company registration form
├── super-admin/
│   ├── dashboard.blade.php           # Super admin dashboard
│   └── tenants/
│       └── index.blade.php           # Tenant management
└── ...
```

## 🔧 Configuration

### **Routes**

```php
// Tenant Registration (Public)
Route::get('/tenant/register', [TenantController::class, 'showRegistration']);
Route::post('/tenant/register', [TenantController::class, 'register']);

// Super Admin (Protected)
Route::get('/super-admin/dashboard', [SuperAdminController::class, 'dashboard']);
Route::get('/super-admin/tenants', [SuperAdminController::class, 'tenants']);
// ... more super admin routes
```

### **Middleware**

The `TenantMiddleware` is automatically applied to all web routes and:

-   Skips tenant switching for registration, admin, and auth routes
-   Finds tenant by user email
-   Switches database connection automatically

## 🧪 Testing

### **Create Test Tenant**

```bash
php artisan tenant:test-create "Test Company Ltd" "test@company.com" --owner_name="John Doe"
```

### **List All Tenants**

```bash
php artisan tenant:list
```

### **Check Tenant Database**

```bash
php artisan db:show | findstr wms_
```

## 📊 Current Status

✅ **Completed Features:**

-   [x] Tenant registration with isolated databases
-   [x] Automatic database switching based on user login
-   [x] Super admin dashboard for tenant management
-   [x] Complete tenant database schema creation
-   [x] Level 5 super user creation for tenant owners
-   [x] Email verification integration
-   [x] Login redirects to appropriate dashboard
-   [x] Tenant suspension/activation
-   [x] Database cleanup on tenant deletion

✅ **System Tested:**

-   Tenant creation: ✅ Working
-   Database isolation: ✅ Working
-   User authentication: ✅ Working
-   Database switching: ✅ Working
-   Super admin access: ✅ Working

## 🔐 Access Levels

### **Super Admin (Level 5)**

-   Access to `/super-admin/*` routes
-   Can manage all tenants
-   Can create/suspend/delete tenants
-   Can login as any tenant user

### **Tenant Owner (Level 5 in tenant DB)**

-   Full admin rights within their tenant database
-   Can manage users, settings, company details
-   Cannot access other tenants

### **Regular Users**

-   Access based on their role and admin level within tenant
-   Completely isolated from other tenants

## 🔄 Usage Flow

### **For New Companies:**

1. Visit login page → Click "Register Your Company"
2. Fill out registration form
3. System creates isolated database and super user
4. Redirected to login page
5. Login with registered credentials
6. Start using system in isolated environment

### **For Existing Users:**

1. Login with email/password
2. System automatically switches to correct tenant database
3. Continue using system normally

### **For Super Admins:**

1. Login with super admin credentials
2. Redirected to super admin dashboard
3. Manage tenants, view statistics
4. Can login as any tenant for support

## 📝 Next Steps

1. **Production Deployment:**

    - Set up proper database backups for tenant databases
    - Configure proper email verification
    - Set up monitoring for tenant databases

2. **Enhanced Features:**

    - Tenant billing and subscription management
    - Data export/import between tenants
    - Tenant-specific customizations
    - Usage analytics per tenant

3. **Performance Optimization:**
    - Database connection pooling
    - Caching strategies for tenant data
    - Optimize tenant lookup queries

## 🎉 Congratulations!

Your multi-tenant system is now fully functional! Each company that registers will get:

-   Completely isolated database
-   Level 5 super admin account
-   Full workflow management system
-   30-day trial period

The system automatically handles database switching, user authentication, and tenant management through an intuitive super admin interface.
