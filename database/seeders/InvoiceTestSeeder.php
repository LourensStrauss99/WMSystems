<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Client;

class InvoiceTestSeeder extends Seeder
{
    public function run()
    {
        $clientIds = Client::pluck('id')->toArray();
        $statuses = ['invoiced', 'paid'];
        $dateOffsets = [30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 330];

        for ($i = 1; $i <= 20; $i++) {
            $clientId = $clientIds[array_rand($clientIds)];
            $daysAgo = $dateOffsets[array_rand($dateOffsets)];
            $invoiceDate = Carbon::now()->subDays($daysAgo);
            $status = $statuses[array_rand($statuses)];

            // For paid invoices, set payment_date; for invoiced, leave null
            $paymentDate = $status === 'paid'
                ? $invoiceDate->copy()->addDays(rand(10, $daysAgo - 1))
                : null;

            Invoice::create([
                'client_id'      => $clientId,
                'invoice_number' => 'INV-' . $invoiceDate->format('Ymd') . '-' . Str::padLeft($i, 4, '0'),
                'amount'         => 800.00,
                'invoice_date'   => $invoiceDate->toDateString(),
                'payment_date'   => $paymentDate,
                'status'         => $status,
            ]);
        }
    }
}