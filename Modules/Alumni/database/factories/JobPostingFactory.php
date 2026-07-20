<?php

namespace Modules\Alumni\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Alumni\Models\JobPosting;

/**
 * @extends Factory<JobPosting>
 */
class JobPostingFactory extends Factory
{
    protected $model = JobPosting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('JOB-###')),
            'name' => fake()->jobTitle(),
            'status' => 'active',
            'description' => fake()->paragraph(),
            'company' => fake()->company(),
            'role_title' => fake()->jobTitle(),
            'location' => fake()->city(),
            'employment_type' => 'full_time',
            'application_method' => 'external_url',
            'application_url' => fake()->url(),
            'application_email' => null,
            'expires_at' => now()->addMonth(),
            'meta' => [],
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
