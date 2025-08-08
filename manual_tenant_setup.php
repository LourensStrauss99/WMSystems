<?php
require 'vendor/autoload.php';

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Manual Database Creation for Tenant 3 ===\n\n";

try {
    $tenant = Tenant::find(3);
    if (!$tenant) {
        echo "✗ Tenant 3 not found\n";
        exit(1);
    }
    
    echo "Found tenant: {$tenant->name}\n";
    echo "Database name should be: {$tenant->database_name}\n";
    
    // Try to create the database directly
    $databaseName = $tenant->database_name;
    echo "Creating database: {$databaseName}\n";
    
    DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database created successfully\n";
    
    // Test if we can connect to it
    $tenant->run(function() {
        $tableCount = DB::select("SHOW TABLES");
        echo "✓ Connected to tenant database, tables: " . count($tableCount) . "\n";
    });
    
    // Try to run migrations
    echo "Running tenant migrations...\n";
    $exitCode = 0;
    $output = [];
    exec("php artisan tenants:migrate --tenants={$tenant->id} 2>&1", $output, $exitCode);
    
    if ($exitCode === 0) {
        echo "✓ Migrations completed successfully\n";
        
        // Check final table count
        $tenant->run(function() {
            $tableCount = DB::select("SHOW TABLES");
            echo "✓ Final table count: " . count($tableCount) . "\n";
        });
    } else {
        echo "✗ Migration failed:\n";
        echo implode("\n", $output) . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
