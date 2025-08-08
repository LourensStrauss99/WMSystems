<?php
require 'vendor/autoload.php';

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $tenant = Tenant::find(1);
    if (!$tenant) {
        echo "✗ Tenant 1 not found\n";
        exit(1);
    }
    
    echo "Creating tenant owner for tenant: {$tenant->name}\n";
    
    $tenant->run(function() use ($tenant) {
        // Check if owner already exists
        $existingUser = User::where('email', $tenant->owner_email ?? 'admin@wmrs.test')->first();
        
        if ($existingUser) {
            echo "✓ Tenant owner already exists: {$existingUser->name} ({$existingUser->email})\n";
            return;
        }
        
        // Create the tenant owner
        $user = User::create([
            'name' => $tenant->owner_name ?? 'WMRS Admin',
            'email' => $tenant->owner_email ?? 'admin@wmrs.test',
            'password' => Hash::make('password123'), // Default password for testing
            'email_verified_at' => now(),
            'role' => 'super_admin',
            'is_superuser' => true,
            'admin_level' => 5,
            'telephone' => $tenant->owner_phone ?? null,
            'is_active' => true,
            'is_first_user' => true,
        ]);
        
        echo "✓ Tenant owner created: {$user->name} ({$user->email})\n";
        echo "✓ Password: password123 (change after login)\n";
        echo "✓ Admin level: {$user->admin_level}\n";
    });
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
