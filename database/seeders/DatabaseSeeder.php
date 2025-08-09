<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This seeder is intentionally empty to ensure tenant databases
     * are created with no pre-existing data.
     */
    public function run(): void
    {
        // Intentionally left empty - tenant databases should start clean
        // Only the tenant owner user should exist, which is created in the controller
    }
}
