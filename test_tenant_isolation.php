<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

echo "=== Testing Database Isolation ===\n";

// Test central database connection
DB::setDefaultConnection('mysql');
echo "Central database connection: " . DB::getDefaultConnection() . "\n";
echo "Central tenant count: " . Tenant::count() . "\n";

// Test tenant database connection
DB::setDefaultConnection('tenant');
echo "\nTenant database connection: " . DB::getDefaultConnection() . "\n";

// Try to access tenant database without tenancy initialized (should fail)
try {
    $tenantConfig = DB::connection('tenant')->getConfig();
    echo "Tenant database name: " . ($tenantConfig['database'] ?? 'null') . "\n";
} catch (Exception $e) {
    echo "Expected error - no tenant database set: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
