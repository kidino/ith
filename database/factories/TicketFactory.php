<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\Category;
use App\Models\TicketStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph,
            'user_id' => User::whereIn('user_type', ['admin', 'it', 'user'])->inRandomOrder()->value('id'),
            'category_id' => Category::inRandomOrder()->value('id'),
            'ticket_status_id' => TicketStatus::inRandomOrder()->value('id'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($ticket) {
            // Attach 1-3 random users as assignees
            $userIds = User::inRandomOrder()->limit(rand(1, 5))->pluck('id');
            $ticket->assignees()->attach($userIds);
        });
    }
}
