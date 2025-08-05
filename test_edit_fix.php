<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventory;

echo "Testing inventory edit form fields:\n\n";

$item = Inventory::first();
if ($item) {
    echo "✅ Sample item found:\n";
    echo "ID: " . $item->id . "\n";
    echo "Description: " . $item->description . "\n";
    echo "Short Code: " . $item->short_code . "\n";
    echo "Department: " . ($item->department ?: 'NULL') . "\n";
    echo "Quantity: " . $item->quantity . "\n";
    echo "Min Quantity: " . $item->min_quantity . "\n";
    echo "Buying Price: " . $item->buying_price . "\n";
    echo "Selling Price: " . $item->selling_price . "\n";
    
    echo "\n✅ Required fields present for edit form:\n";
    echo "- description: " . (isset($item->description) ? 'YES' : 'NO') . "\n";
    echo "- quantity: " . (isset($item->quantity) ? 'YES' : 'NO') . "\n";
    echo "- min_quantity: " . (isset($item->min_quantity) ? 'YES' : 'NO') . "\n";
    echo "- department: " . (isset($item->department) ? 'YES' : 'NO') . "\n";
    
} else {
    echo "❌ No inventory items found in database\n";
}

echo "\n✅ Available departments:\n";
$departments = Inventory::getDepartmentOptions();
foreach ($departments as $code => $name) {
    echo "- $code: $name\n";
}
