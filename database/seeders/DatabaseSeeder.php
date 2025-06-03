<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Database\Seeders\JobcardTestInvoiceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test10 User',
            'email' => 'test10@example.com',
        ]);

        // Ensure the seeder exists: database/seeders/JobcardTestInvoiceSeeder.php
       // $this->call(\Database\Seeders\InvoiceTestSeeder::class);
       // $this->call(\Database\Seeders\ElectricalSparesSeeder::class);
       $this->call(\Database\Seeders\PlumbingSparesSeeder::class);
    }
}
