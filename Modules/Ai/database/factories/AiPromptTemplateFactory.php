<?php

namespace Modules\Ai\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Ai\Models\AiPromptTemplate;

/**
 * @extends Factory<AiPromptTemplate>
 */
class AiPromptTemplateFactory extends Factory
{
    protected $model = AiPromptTemplate::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('AI-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => ['feature' => 'advisor'],
        ];
    }
}
