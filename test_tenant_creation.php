<?php

// Bootstrap Laravel to load classes
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simple test script to check tenant creation and database creation
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "Testing tenant creation pipeline...\n";

try {
    // Create a test tenant with all required fields to match current schema
    $tenant = new Tenant([
        'id' => 999,
        'name' => 'Test Company',
        'slug' => 'test-company',
        'database_name' => 'tenant_999',
        'owner_name' => 'Test Owner',
        'owner_email' => 'test@example.com',
        'owner_password' => bcrypt('password'),
        'address' => 'Test Address',
        'city' => 'Test City',
        'country' => 'Test Country',
        'status' => 'active',
        'is_active' => true,
        'payment_status' => 'active',
        'monthly_fee' => 0,
        'subscription_plan' => 'basic',
    ]);
    
    echo "Saving tenant...\n";
    $tenant->save();
    
    echo "Tenant created with ID: {$tenant->id}\n";
    echo "Database name should be: {$tenant->database_name}\n";
    
    // Check if database was created
    $databases = DB::select('SHOW DATABASES');
    $dbNames = array_column($databases, 'Database');
    
    if (in_array('tenant_999', $dbNames)) {
        echo "✓ Database 'tenant_999' was created successfully!\n";
    } else {
        echo "✗ Database 'tenant_999' was NOT created.\n";
        echo "Available databases: " . implode(', ', $dbNames) . "\n";
    }
    
    // Clean up
    $tenant->delete();
    DB::statement('DROP DATABASE IF EXISTS tenant_999');
    echo "Test cleanup completed.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
