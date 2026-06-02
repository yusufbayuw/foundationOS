<?php

namespace Modules\Transport\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Transport\Models\Route;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    protected $model = Route::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('RT-###')),
            'name' => fake()->words(3, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
