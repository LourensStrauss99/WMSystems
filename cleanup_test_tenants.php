<?php
require 'vendor/autoload.php';

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Cleaning up test tenant databases ===\n\n";

try {
    // Clean up test databases
    $testDatabases = ['tenant2', 'tenant3', 'tenant4', 'tenant_3'];
    
    foreach ($testDatabases as $dbName) {
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
            echo "✓ Dropped database: {$dbName}\n";
        } catch (Exception $e) {
            echo "✗ Failed to drop {$dbName}: " . $e->getMessage() . "\n";
        }
    }
    
    // Clean up test tenant records
    $testTenants = Tenant::where('id', '>', 1)->get();
    foreach ($testTenants as $tenant) {
        echo "Deleting tenant: {$tenant->name} (ID: {$tenant->id})\n";
        $tenant->delete();
    }
    
    echo "\n✓ Cleanup completed\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
