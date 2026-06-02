<?php

namespace Modules\Risk\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Risk\Models\RiskCategory;

/**
 * @extends Factory<RiskCategory>
 */
class RiskCategoryFactory extends Factory
{
    protected $model = RiskCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('RC-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
