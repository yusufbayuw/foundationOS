<?php

namespace Modules\Voucher\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Voucher\Models\Voucher;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'code' => Str::upper(fake()->unique()->bothify('VCHR-####')),
            'quota' => 10,
            'claimed_count' => 0,
            'start_at' => now()->subDay(),
            'end_at' => now()->addWeek(),
            'status' => 'active',
            'terms' => fake()->sentence(),
            'redemption_method' => 'manual',
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'start_at' => now()->subDays(3),
            'end_at' => now()->subDay(),
        ]);
    }
}
