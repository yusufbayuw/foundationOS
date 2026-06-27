<?php

namespace Modules\Facility\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Facility\Models\Room;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('RM-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
            'is_bookable' => true,
            'is_rentable' => false,
        ];
    }
}
