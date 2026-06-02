<?php

namespace Modules\Sales\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Sales\Models\Customer;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CUS-####')),
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'is_active' => true,
            'is_cooperative_member' => false,
            'member_number' => null,
            'member_discount_percent' => 0,
        ];
    }

    public function cooperative(string $memberNumber = 'M-001', float $discountPercent = 5): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_cooperative_member' => true,
            'member_number' => $memberNumber,
            'member_discount_percent' => $discountPercent,
        ]);
    }
}
