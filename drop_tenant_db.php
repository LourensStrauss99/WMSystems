<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Drop the tenant database
    DB::statement('DROP DATABASE IF EXISTS tenant_1');
    echo "✓ Database 'tenant_1' dropped successfully!\n";
    
    // Create it again
    DB::statement("CREATE DATABASE tenant_1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'tenant_1' created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
