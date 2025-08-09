<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Initialize the application
$app->make('kernel')->bootstrap();

// Initialize tenancy
tenancy()->initialize(21);

// Check database tables
$tables = DB::select('SHOW TABLES');
echo "Tables in tenant 21:\n";
foreach ($tables as $table) {
    $tableName = array_values((array) $table)[0];
    echo "- $tableName\n";
    
    if ($tableName === 'invoices') {
        $count = DB::table('invoices')->count();
        echo "  -> Has $count invoices\n";
        
        if ($count > 0) {
            $invoices = DB::table('invoices')->limit(3)->get(['id', 'invoice_number', 'total_amount']);
            foreach ($invoices as $invoice) {
                echo "  -> Invoice ID: {$invoice->id}, Number: {$invoice->invoice_number}, Amount: {$invoice->total_amount}\n";
            }
        }
    }
}
