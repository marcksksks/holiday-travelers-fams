<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FacilityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Room',
            'facility_type' => 'meeting_room',
            'capacity' => fake()->numberBetween(4, 20),
            'status' => 'available',
        ];
    }
}
