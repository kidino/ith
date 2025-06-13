<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\TicketStatusSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\VendorSeeder;
use Database\Seeders\TicketSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TicketStatusSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(VendorSeeder::class);
        User::factory(40)->create();
        $this->call(TicketSeeder::class);
    }
}
