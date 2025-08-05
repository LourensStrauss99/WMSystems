<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventory;

echo "Testing Inventory Creation:\n\n";

// Test creating an inventory item
try {
    $inventory = new Inventory();
    $inventory->short_code = Inventory::generateInventoryCode('EL');
    $inventory->description = 'Test Electrical Component';
    $inventory->department = 'EL';
    $inventory->cost = 50.00;
    $inventory->markup_percentage = 20;
    $inventory->selling_price = 60.00;
    $inventory->quantity = 10;
    
    echo "Generated inventory data:\n";
    echo "Short Code: " . $inventory->short_code . "\n";
    echo "Description: " . $inventory->description . "\n";
    echo "Department: " . $inventory->department . "\n";
    echo "Cost: " . $inventory->cost . "\n";
    echo "Markup: " . $inventory->markup_percentage . "%\n";
    echo "Selling Price: " . $inventory->selling_price . "\n";
    echo "Quantity: " . $inventory->quantity . "\n";
    
    // Don't actually save to avoid duplicate records, just test the generation
    echo "\nInventory object created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
