<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPackage;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic Plan',
                'slug' => 'basic',
                'description' => 'Perfect for small businesses just getting started',
                'monthly_price' => 4500.00,
                'yearly_price' => 45000.00,
                'max_users' => 5,
                'storage_limit_mb' => 10240, // 10GB in MB
                'features' => [
                    'Job Card Management',
                    'Client Management',
                    'Basic Inventory',
                    'Quote Generation',
                    'Basic Reports',
                    'Email Support'
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Standard Plan',
                'slug' => 'standard',
                'description' => 'Ideal for growing businesses with advanced needs',
                'monthly_price' => 6000.00,
                'yearly_price' => 60000.00,
                'max_users' => 15,
                'storage_limit_mb' => 51200, // 50GB in MB
                'features' => [
                    'Everything in Basic',
                    'Advanced Inventory Management',
                    'Purchase Orders',
                    'Invoice Management',
                    'Advanced Reports',
                    'Mobile App Access',
                    'Priority Support'
                ],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Premium Plan',
                'slug' => 'premium',
                'description' => 'For established businesses requiring full functionality',
                'monthly_price' => 8000.00,
                'yearly_price' => 80000.00,
                'max_users' => 50,
                'storage_limit_mb' => 204800, // 200GB in MB
                'features' => [
                    'Everything in Standard',
                    'Multi-location Support',
                    'Advanced Analytics',
                    'Custom Reports',
                    'API Access',
                    'White-label Options',
                    'Dedicated Support'
                ],
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Enterprise Plan',
                'slug' => 'enterprise',
                'description' => 'Scalable solution for large organizations',
                'monthly_price' => 10000.00,
                'yearly_price' => 100000.00,
                'max_users' => 999,
                'storage_limit_mb' => 1048576, // 1TB in MB
                'features' => [
                    'Everything in Premium',
                    'Unlimited Users',
                    'Custom Integrations',
                    'Advanced Security',
                    'Compliance Features',
                    'Custom Training',
                    '24/7 Phone Support',
                    'Account Manager'
                ],
                'is_active' => true,
                'sort_order' => 4
            ]
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::create($package);
        }
    }
}
