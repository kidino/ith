<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('VEND??')),
            'name' => fake()->company,
            'phone_number' => fake()->phoneNumber,
            'address' => fake()->address,
            'person_in_charge' => fake()->name,
            'email' => fake()->unique()->companyEmail,
        ];
    }
}
