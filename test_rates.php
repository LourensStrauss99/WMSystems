<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test CompanyDetail rates
echo "=== CompanyDetail Model Rates ===" . PHP_EOL;
$companyDetail = App\Models\CompanyDetail::first();
if ($companyDetail) {
    echo "Labour Rate: " . ($companyDetail->labour_rate ?? 'null') . PHP_EOL;
    echo "Call Out Rate: " . ($companyDetail->call_out_rate ?? 'null') . PHP_EOL;
    echo "VAT Percent: " . ($companyDetail->vat_percent ?? 'null') . PHP_EOL;
} else {
    echo "No CompanyDetail record found" . PHP_EOL;
}

echo PHP_EOL . "=== Company::getSettings() Rates ===" . PHP_EOL;
$companySettings = App\Models\Company::getSettings();
echo "Standard Labour Rate: " . ($companySettings['standard_labour_rate'] ?? 'null') . PHP_EOL;
echo "Call Out Rate: " . ($companySettings['call_out_rate'] ?? 'null') . PHP_EOL;
echo "VAT Percentage: " . ($companySettings['vat_percentage'] ?? 'null') . PHP_EOL;
