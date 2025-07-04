<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUsersTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users table structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Users Table Structure:");
        
        $columns = DB::select("SHOW COLUMNS FROM users");
        
        foreach ($columns as $column) {
            $this->line("- {$column->Field} ({$column->Type}) " . 
                      ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . 
                      ($column->Default ? " DEFAULT: {$column->Default}" : ""));
        }
    }
}
