<?php

namespace Modules\Helpdesk\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Helpdesk\Models\TicketCategory;

/**
 * @extends Factory<TicketCategory>
 */
class TicketCategoryFactory extends Factory
{
    protected $model = TicketCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('HD-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'response_hours' => 4,
            'resolution_hours' => 24,
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
