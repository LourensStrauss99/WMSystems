<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyDetail;

class CompanyDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompanyDetail::updateOrCreate(
            ['company_name' => 'Acme Corporation'],
            [
                'labour_rate' => 750.00,
                'vat_percent' => 15.00,
                'company_name' => 'Acme Corporation',
                'company_reg_number' => '123456789',
                'vat_reg_number' => 'VAT987654321',
                'bank_name' => 'First National Bank',
                'account_holder' => 'Acme Corporation Ltd',
                'account_number' => '123456789012',
                'branch_code' => '250655',
                'swift_code' => 'FIRNZAJJXXX',
                'address' => '123 Business Avenue',
                'city' => 'Cape Town',
                'province' => 'Western Cape',
                'postal_code' => '8001',
                'country' => 'South Africa',
                'company_telephone' => '+27 21 123 4567',
                'company_email' => 'info@acmecorp.com',
                'company_website' => 'https://www.acmecorp.com',
                'invoice_terms' => 'Payment due within 30 days of invoice date.',
                'invoice_footer' => 'Thank you for your business! All payments to be made via EFT.',
                'company_logo' => 'storage/logos/acme_logo.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}