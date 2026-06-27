<?php

namespace Modules\Marketplace\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Marketplace\Models\Seller;

/**
 * @extends Factory<Seller>
 */
class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('SLR-###')),
            'name' => fake()->company(),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
            'verification_status' => 'pending',
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_status' => 'verified',
        ]);
    }
}
