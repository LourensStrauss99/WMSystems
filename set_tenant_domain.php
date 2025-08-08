<?php
require 'vendor/autoload.php';

use App\Models\Tenant;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Setting Domain for Tenant 1 ===\n\n";

try {
    $tenant = Tenant::find(1);
    
    if ($tenant) {
        echo "Found tenant: {$tenant->name}\n";
        echo "Current domain: " . ($tenant->domain ?: 'Not set') . "\n";
        
        // Set the domain to 'wmrs' for subdomain access
        $tenant->domain = 'wmrs';
        $tenant->save();
        
        echo "✓ Updated domain to: {$tenant->domain}\n";
        echo "✓ Tenant should now be accessible at: http://wmrs.workflow-management.test/\n";
        
    } else {
        echo "✗ Tenant 1 not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
