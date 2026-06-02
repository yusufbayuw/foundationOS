<?php

namespace Modules\MerchOrder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MerchOrder\Models\UniformPackage;

/**
 * @extends Factory<UniformPackage>
 */
class UniformPackageFactory extends Factory
{
    protected $model = UniformPackage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('UNI-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
