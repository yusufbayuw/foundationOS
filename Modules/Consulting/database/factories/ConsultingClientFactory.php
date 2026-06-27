<?php

namespace Modules\Consulting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Consulting\Models\ConsultingClient;

/**
 * @extends Factory<ConsultingClient>
 */
class ConsultingClientFactory extends Factory
{
    protected $model = ConsultingClient::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CLI-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
