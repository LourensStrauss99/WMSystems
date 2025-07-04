<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckCompanyTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:company-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check company_details table structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Company Details Table Structure:");
        
        $columns = DB::select("SHOW COLUMNS FROM company_details");
        
        foreach ($columns as $column) {
            $this->line("- {$column->Field} ({$column->Type}) " . 
                      ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . 
                      ($column->Default ? " DEFAULT: {$column->Default}" : ""));
        }
    }
}
