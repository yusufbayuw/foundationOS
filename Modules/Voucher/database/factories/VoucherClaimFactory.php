<?php

namespace Modules\Voucher\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Voucher\Models\Voucher;
use Modules\Voucher\Models\VoucherClaim;

/**
 * @extends Factory<VoucherClaim>
 */
class VoucherClaimFactory extends Factory
{
    protected $model = VoucherClaim::class;

    public function definition(): array
    {
        return [
            'voucher_id' => Voucher::factory(),
            'user_id' => User::factory(),
            'claim_code' => 'CLM-'.Str::upper(Str::random(10)),
            'status' => 'claimed',
            'claimed_at' => now(),
            'used_at' => null,
        ];
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'used',
            'used_at' => now(),
        ]);
    }
}
