<?php

namespace Modules\Boarding\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Boarding\Models\Dormitory;

/**
 * @extends Factory<Dormitory>
 */
class DormitoryFactory extends Factory
{
    protected $model = Dormitory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('DORM-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
