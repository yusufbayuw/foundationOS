<?php

namespace Modules\Donation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Donation\Models\Donor;

/**
 * @extends Factory<Donor>
 */
class DonorFactory extends Factory
{
    protected $model = Donor::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'is_anonymous' => false,
            'tags' => [],
        ];
    }

    public function anonymous(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_anonymous' => true,
            'email' => null,
            'name' => 'Anonymous Donor',
        ]);
    }
}
