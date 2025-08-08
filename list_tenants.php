<?php
require 'vendor/autoload.php';

use App\Models\Tenant;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Current Tenants in Database ===\n\n";

try {
    $tenants = Tenant::all();
    
    if ($tenants->count() > 0) {
        foreach ($tenants as $tenant) {
            echo "ID: {$tenant->id}\n";
            echo "Name: {$tenant->name}\n";
            echo "Domain: {$tenant->domain}\n";
            echo "Database: " . ($tenant->database_name ?: 'Not set') . "\n";
            echo "Owner: {$tenant->owner_name} ({$tenant->owner_email})\n";
            echo "---\n";
        }
    } else {
        echo "No tenants found in database.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
