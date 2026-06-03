<?php

namespace Modules\EducationQa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\EducationQa\Models\QualityStandard;

/**
 * @extends Factory<QualityStandard>
 */
class QualityStandardFactory extends Factory
{
    protected $model = QualityStandard::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('QST-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
