<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;

echo "Creating database for tenant 1...\n";

try {
    $tenant = Tenant::find(1);
    if ($tenant) {
        $tenant->database()->manager()->createDatabase($tenant);
        echo "✓ Database '{$tenant->database()->getName()}' created successfully!\n";
    } else {
        echo "✗ Tenant 1 not found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
