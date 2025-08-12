<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tenantId = (int) ($argv[1] ?? 22);

echo "Initializing tenant {$tenantId}...\n";
tenancy()->initialize($tenantId);

$dbName = DB::getDatabaseName();
echo "Tenant connection database: {$dbName}\n";

// Try a safe insert into users table
$email = 'probe+' . uniqid('', true) . '@example.com';
$inserted = false;

try {
    $inserted = DB::table('users')->insert([
        'name' => 'Probe User',
        'email' => $email,
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Inserted tenant user email: {$email}\n";
} catch (Throwable $e) {
    echo "Insert failed: {$e->getMessage()}\n";
}

$count = DB::table('users')->count();
echo "Users count (tenant DB): {$count}\n";

tenancy()->end();

echo "Reverted to central. Current DB (should be central): " . DB::getDatabaseName() . "\n";




