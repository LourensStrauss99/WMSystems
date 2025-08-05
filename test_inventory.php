<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventory;

echo "Testing Inventory Code Generation:\n\n";

$departments = ['EL', 'PL', 'SU', 'WS', 'TL'];

foreach ($departments as $dept) {
    $code = Inventory::generateInventoryCode($dept);
    echo "$dept: $code\n";
}

echo "\nTesting Department Options:\n";
$options = Inventory::getDepartmentOptions();
foreach ($options as $code => $name) {
    echo "$code => $name\n";
}
