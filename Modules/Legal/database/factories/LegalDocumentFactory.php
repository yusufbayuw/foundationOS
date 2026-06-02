<?php

namespace Modules\Legal\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Legal\Models\LegalDocument;

/**
 * @extends Factory<LegalDocument>
 */
class LegalDocumentFactory extends Factory
{
    protected $model = LegalDocument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('LEG-###')),
            'name' => fake()->words(3, true),
            'document_type' => fake()->randomElement(['policy', 'regulation', 'agreement']),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
