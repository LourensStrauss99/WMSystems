<?php
require 'vendor/autoload.php';

use App\Models\Tenant;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $tenant = Tenant::find(1);
    if ($tenant) {
        $tenant->run(function() {
            $user = User::where('admin_level', 5)->first();
            if ($user) {
                echo "✓ Tenant Owner found: {$user->name} ({$user->email})\n";
                echo "✓ Admin level: {$user->admin_level}\n";
            } else {
                echo "✗ No tenant owner found with admin_level 5\n";
            }
            
            $totalUsers = User::count();
            echo "✓ Total users in tenant database: {$totalUsers}\n";
        });
    } else {
        echo "✗ Tenant 1 not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
