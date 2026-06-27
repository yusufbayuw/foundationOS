<?php

namespace Modules\Clinic\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Clinic\Models\Allergy;

/**
 * @extends Factory<Allergy>
 */
class AllergyFactory extends Factory
{
    protected $model = Allergy::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('ALG-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
