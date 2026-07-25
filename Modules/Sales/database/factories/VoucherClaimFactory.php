<?php

namespace Modules\Sales\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Sales\Models\VoucherClaim;

/**
 * @extends Factory<VoucherClaim>
 */
class VoucherClaimFactory extends Factory
{
    protected $model = VoucherClaim::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'claim_code' => strtoupper(fake()->unique()->bothify('CLM-####')),
            'status' => 'claimed',
            'claimed_at' => now(),
            'used_at' => null,
        ];
    }
}
