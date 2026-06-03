<?php

namespace Modules\Capacity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Capacity\Models\CapacityResource;

/**
 * @extends Factory<CapacityResource>
 */
class CapacityResourceFactory extends Factory
{
    protected $model = CapacityResource::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CAP-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
