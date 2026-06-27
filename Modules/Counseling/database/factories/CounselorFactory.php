<?php

namespace Modules\Counseling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Counseling\Models\Counselor;

/**
 * @extends Factory<Counselor>
 */
class CounselorFactory extends Factory
{
    protected $model = Counselor::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CNS-###')),
            'name' => fake()->name(),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
