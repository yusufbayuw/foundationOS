<?php

namespace Modules\Printing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Printing\Models\PrintTemplate;

/**
 * @extends Factory<PrintTemplate>
 */
class PrintTemplateFactory extends Factory
{
    protected $model = PrintTemplate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PRT-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
