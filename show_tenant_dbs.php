<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $databases = DB::select('SHOW DATABASES');
    echo "Tenant databases:\n";
    foreach ($databases as $db) {
        if (strpos($db->Database, 'tenant') !== false) {
            echo "- {$db->Database}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
