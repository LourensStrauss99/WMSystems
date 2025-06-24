<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'ABC Electronics Supply',
                'contact_person' => 'John Smith',
                'email' => 'john@abcelectronics.com',
                'phone' => '+27 11 123 4567',
                'address' => '123 Industrial Road',
                'city' => 'Johannesburg',
                'postal_code' => '2001',
                'payment_terms' => '30_days',
                'credit_limit' => 50000.00,
                'active' => true,
            ],
            [
                'name' => 'XYZ Hardware Supplies',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@xyzhardware.co.za',
                'phone' => '+27 21 987 6543',
                'address' => '456 Commerce Street',
                'city' => 'Cape Town',
                'postal_code' => '8001',
                'payment_terms' => '60_days',
                'credit_limit' => 75000.00,
                'active' => true,
            ],
            [
                'name' => 'Tech Components Direct',
                'contact_person' => 'Mike Wilson',
                'email' => 'mike@techcomponents.com',
                'phone' => '+27 31 555 7890',
                'address' => '789 Technology Park',
                'city' => 'Durban',
                'postal_code' => '4001',
                'payment_terms' => '30_days',
                'credit_limit' => 100000.00,
                'active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
