<?php
require 'vendor/autoload.php';

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Complete Tenant Creation Pipeline ===\n\n";

try {
    // Get count before creation
    $beforeCount = Tenant::count();
    echo "Tenants before creation: {$beforeCount}\n";
    
    // Get next available ID
    $nextId = Tenant::max('id') + 1;
    
    // Create a new tenant programmatically to test the pipeline
    $tenant = Tenant::create([
        'id' => $nextId,
        'name' => 'Test Company ' . date('H:i:s'),
        'slug' => 'test-company-' . $nextId,
        'database_name' => 'tenant_' . $nextId,
        'domain' => 'test' . $nextId,
        'owner_name' => 'Test Owner',
        'owner_email' => 'test' . $nextId . '@example.com',
        'owner_phone' => '123-456-7890'
    ]);
    
    echo "✓ Tenant created: {$tenant->name} (ID: {$tenant->id})\n";
    
    // Wait a moment for background jobs to process
    sleep(2);
    
    // Check if database was created
    $databaseName = "tenant_{$tenant->id}";
    try {
        $dbExists = DB::statement("USE {$databaseName}");
        echo "✓ Database '{$databaseName}' exists\n";
        
        // Check if migrations ran
        $tenant->run(function() {
            $tableCount = DB::select("SHOW TABLES");
            echo "✓ Tables created: " . count($tableCount) . "\n";
            
            // Check if tenant owner was created
            $user = User::where('admin_level', 5)->first();
            if ($user) {
                echo "✓ Tenant owner created: {$user->name} ({$user->email})\n";
            } else {
                echo "✗ No tenant owner found\n";
            }
        });
        
    } catch (Exception $e) {
        echo "✗ Database '{$databaseName}' does not exist: " . $e->getMessage() . "\n";
    }
    
    $afterCount = Tenant::count();
    echo "\nTenants after creation: {$afterCount}\n";
    echo "✓ Tenant creation test completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error during tenant creation: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
