<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Drop the existing tenants table
\Illuminate\Support\Facades\DB::statement('DROP TABLE IF EXISTS tenants');

echo "Tenants table dropped successfully!\n";
