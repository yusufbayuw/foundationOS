<?php

namespace Modules\ItOps\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ItOps\Models\SoftwareLicense;

/**
 * @extends Factory<SoftwareLicense>
 */
class SoftwareLicenseFactory extends Factory
{
    protected $model = SoftwareLicense::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('LIC-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
