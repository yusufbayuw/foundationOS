<?php

namespace Modules\IsoCompliance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\IsoCompliance\Models\IsoControl;

/**
 * @extends Factory<IsoControl>
 */
class IsoControlFactory extends Factory
{
    protected $model = IsoControl::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('ISO-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
