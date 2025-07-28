<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jobcard;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class BackfillJobcardInventoryCost extends Command
{
    protected $signature = 'jobcards:backfill-inventory-cost';
    protected $description = 'Backfill jobcard inventory pivot costs with current inventory buying_price if missing.';

    public function handle()
    {
        $updated = 0;
        $skipped = 0;
        $total = 0;
        $this->info('Starting backfill of jobcard inventory costs...');
        Jobcard::with('inventory')->chunk(100, function($jobcards) use (&$updated, &$skipped, &$total) {
            foreach ($jobcards as $jobcard) {
                foreach ($jobcard->inventory as $item) {
                    $pivot = $item->pivot;
                    $total++;
                    if (empty($pivot->buying_price) || $pivot->buying_price == 0) {
                        $item->pivot->buying_price = $item->buying_price ?? 0;
                        $item->pivot->selling_price = $item->selling_price ?? $item->sell_price ?? 0;
                        $item->pivot->save();
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }
            }
        });
        $this->info("Backfill complete. Updated: {$updated}, Skipped: {$skipped}, Total: {$total}");
        $this->info('You can reverse this by setting buying_price to null or 0 on the pivot if needed.');
        return 0;
    }
} 