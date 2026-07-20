<?php

namespace Modules\Library\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Library\Models\Member;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'member_number' => strtoupper(fake()->unique()->bothify('LIB-####')),
            'member_type' => fake()->randomElement(['student', 'staff', 'public']),
            'joined_at' => now()->toDateString(),
            'expires_at' => now()->addYear()->toDateString(),
            'max_books' => 3,
            'loan_period_days' => 7,
            'fine_per_day' => 1000,
            'status' => 'pending',
            'membership_proof' => 'memberships/proof.pdf',
            'profile_data' => [
                'identity_number' => fake()->numerify('################'),
                'phone' => fake()->phoneNumber(),
            ],
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => 'pending']);
    }
}
