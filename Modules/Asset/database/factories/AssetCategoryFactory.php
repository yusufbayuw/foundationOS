<?php

namespace Modules\Asset\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Asset\Models\AssetCategory;

/**
 * @extends Factory<AssetCategory>
 */
class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('AST-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
