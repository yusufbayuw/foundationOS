<?php

namespace Modules\Event\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Event\Models\Event as EventModel;

/**
 * @extends Factory<EventModel>
 */
class EventFactory extends Factory
{
    protected $model = EventModel::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('EVT-###')),
            'name' => fake()->words(3, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
