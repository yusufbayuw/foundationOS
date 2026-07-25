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

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('EVT-###')),
            'name' => fake()->words(3, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [
                'start_at' => now()->addWeek()->toIso8601String(),
                'end_at' => now()->addWeek()->addHours(2)->toIso8601String(),
                'location' => fake()->city(),
                'dresscode' => null,
                'registration_url' => null,
                'cover_image' => null,
            ],
        ];
    }
}
