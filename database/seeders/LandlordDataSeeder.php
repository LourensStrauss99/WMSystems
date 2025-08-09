<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\LandlordInvoice;
use App\Models\LandlordPayment;
use Carbon\Carbon;

class LandlordDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::take(5)->get();
        
        if ($tenants->count() === 0) {
            $this->command->info('No tenants found. Skipping landlord data seeding.');
            return;
        }

        foreach ($tenants as $tenant) {
            // Create invoices for the last 6 months
            for ($month = 0; $month < 6; $month++) {
                $invoiceDate = Carbon::now()->subMonths($month)->startOfMonth();
                $dueDate = $invoiceDate->copy()->addDays(15);
                
                $invoice = LandlordInvoice::create([
                    'invoice_number' => 'INV-' . $tenant->id . '-' . $invoiceDate->format('Ym'),
                    'tenant_id' => $tenant->id,
                    'amount' => $tenant->monthly_fee ?: 4500.00,
                    'tax_amount' => ($tenant->monthly_fee ?: 4500.00) * 0.15, // 15% VAT
                    'total_amount' => ($tenant->monthly_fee ?: 4500.00) * 1.15,
                    'currency' => 'ZAR',
                    'status' => $month == 0 ? 'pending' : 'paid', // Current month pending, others paid
                    'invoice_date' => $invoiceDate,
                    'due_date' => $dueDate,
                    'paid_date' => $month == 0 ? null : $dueDate->copy()->addDays(rand(1, 5)),
                    'billing_period' => 'monthly',
                    'description' => 'Monthly subscription for ' . $invoiceDate->format('F Y'),
                    'line_items' => [
                        [
                            'description' => ucfirst($tenant->subscription_plan) . ' Plan Subscription',
                            'quantity' => 1,
                            'unit_price' => $tenant->monthly_fee ?: 4500.00,
                            'total' => $tenant->monthly_fee ?: 4500.00
                        ]
                    ]
                ]);

                // Create payment for paid invoices
                if ($invoice->status === 'paid') {
                    LandlordPayment::create([
                        'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                        'tenant_id' => $tenant->id,
                        'landlord_invoice_id' => $invoice->id,
                        'amount' => $invoice->total_amount,
                        'currency' => 'ZAR',
                        'payment_method' => 'bank_transfer',
                        'status' => 'completed',
                        'payment_date' => $invoice->paid_date,
                        'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                        'notes' => 'Monthly subscription payment'
                    ]);
                }
            }
        }

        $this->command->info('Created sample landlord invoices and payments for ' . $tenants->count() . ' tenants.');
    }
}
