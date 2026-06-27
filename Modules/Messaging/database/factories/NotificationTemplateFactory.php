<?php

namespace Modules\Messaging\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Messaging\Models\NotificationTemplate;

/**
 * @extends Factory<NotificationTemplate>
 */
class NotificationTemplateFactory extends Factory
{
    protected $model = NotificationTemplate::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('NTF-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
