<?php

namespace Modules\KpiEnterprise\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\KpiEnterprise\Models\KpiArea;

/**
 * @extends Factory<KpiArea>
 */
class KpiAreaFactory extends Factory
{
    protected $model = KpiArea::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('KPI-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
