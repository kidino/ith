<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketStatus;

class TicketStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'new', 'color' => '#2563eb', 'default_status' => true],
            ['name' => 'in_progress', 'color' => '#f59e42', 'default_status' => false],
            ['name' => 'pending_vendor', 'color' => '#fbbf24', 'default_status' => false],
            ['name' => 'pending_user', 'color' => '#fbbf24', 'default_status' => false],
            ['name' => 'resolved', 'color' => '#10b981', 'default_status' => false],
            ['name' => 'closed', 'color' => '#6b7280', 'default_status' => false],
            ['name' => 'reopen', 'color' => '#ef4444', 'default_status' => false],
        ];

        foreach ($statuses as $status) {
            TicketStatus::create($status);
        }
    }
}
