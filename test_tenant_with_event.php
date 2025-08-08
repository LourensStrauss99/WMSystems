<?php
require 'vendor/autoload.php';

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Events\TenantCreated;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Tenant Creation with Event ===\n\n";

try {
    // Get next available ID
    $nextId = Tenant::max('id') + 1;
    
    // Create a new tenant programmatically
    $tenant = Tenant::create([
        'id' => $nextId,
        'name' => 'Auto Test Company ' . date('H:i:s'),
        'slug' => 'auto-test-company-' . $nextId,
        'database_name' => 'tenant' . $nextId, // Use Stancl's naming convention: prefix + id
        'domain' => 'autotest' . $nextId,
        'owner_name' => 'Auto Test Owner',
        'owner_email' => 'autotest' . $nextId . '@example.com',
        'owner_phone' => '123-456-7890'
    ]);
    
    echo "✓ Tenant created: {$tenant->name} (ID: {$tenant->id})\n";
    
    // The TenantCreated event should fire automatically when using Tenant::create()
    // Wait a moment for jobs to process
    sleep(3);
    
    // Check if database was created
    echo "Expected database name: tenant{$tenant->id}\n";
    try {
        $tenant->run(function() use ($tenant) {
            $tableCount = \Illuminate\Support\Facades\DB::select("SHOW TABLES");
            echo "✓ Database 'tenant{$tenant->id}' created with " . count($tableCount) . " tables\n";
            
            // Create tenant owner
            $user = User::create([
                'name' => $tenant->owner_name,
                'email' => $tenant->owner_email,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'super_admin',
                'is_superuser' => true,
                'admin_level' => 5,
                'telephone' => $tenant->owner_phone,
                'is_active' => true,
                'is_first_user' => true,
            ]);
            
            echo "✓ Tenant owner created: {$user->name} ({$user->email})\n";
        });
        
    } catch (Exception $e) {
        echo "✗ Database check failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n✓ Full tenant creation pipeline test completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
