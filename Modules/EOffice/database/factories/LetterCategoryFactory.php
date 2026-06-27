<?php

namespace Modules\EOffice\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\EOffice\Models\LetterCategory;

/**
 * @extends Factory<LetterCategory>
 */
class LetterCategoryFactory extends Factory
{
    protected $model = LetterCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('LTR-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
