<?php

namespace Modules\PhysicalSecurity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PhysicalSecurity\Models\Guard;

/**
 * @extends Factory<Guard>
 */
class GuardFactory extends Factory
{
    protected $model = Guard::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('GRD-###')),
            'name' => fake()->name(),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
