<?php

namespace Modules\Voucher\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\User;
use Modules\Voucher\Models\Voucher;
use Modules\Voucher\Models\VoucherClaim;
use RuntimeException;

class VoucherClaimService
{
    public function claim(Voucher $voucher, User $user): VoucherClaim
    {
        return DB::transaction(function () use ($voucher, $user): VoucherClaim {
            $lockedVoucher = Voucher::query()
                ->whereKey($voucher->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $existingClaim = VoucherClaim::query()
                ->where('voucher_id', $lockedVoucher->getKey())
                ->where('user_id', $user->getKey())
                ->first();

            if ($existingClaim) {
                throw new RuntimeException('duplicate_claim');
            }

            if (! $lockedVoucher->isClaimable()) {
                throw new RuntimeException($lockedVoucher->claimed_count >= $lockedVoucher->quota ? 'quota_exhausted' : 'voucher_unavailable');
            }

            $claim = VoucherClaim::query()->create([
                'voucher_id' => $lockedVoucher->getKey(),
                'user_id' => $user->getKey(),
                'claim_code' => $this->generateClaimCode(),
                'status' => 'claimed',
                'claimed_at' => now(),
            ]);

            $lockedVoucher->increment('claimed_count');

            return $claim->refresh();
        });
    }

    public function redeem(VoucherClaim $claim, User $user, ?string $claimCode): VoucherClaim
    {
        return DB::transaction(function () use ($claim, $user, $claimCode): VoucherClaim {
            $lockedClaim = VoucherClaim::query()
                ->whereKey($claim->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $lockedClaim->user_id !== (int) $user->getKey()) {
                throw new RuntimeException('invalid_claim');
            }

            if ($claimCode !== null && ! hash_equals($lockedClaim->claim_code, $claimCode)) {
                throw new RuntimeException('invalid_claim_code');
            }

            if ($lockedClaim->status !== 'claimed') {
                throw new RuntimeException('claim_not_redeemable');
            }

            $voucher = Voucher::query()->whereKey($lockedClaim->voucher_id)->firstOrFail();

            if ($voucher->end_at !== null && $voucher->end_at->isPast()) {
                $lockedClaim->update(['status' => 'expired']);

                throw new RuntimeException('voucher_expired');
            }

            $lockedClaim->update([
                'status' => 'used',
                'used_at' => now(),
            ]);

            return $lockedClaim->refresh();
        });
    }

    private function generateClaimCode(): string
    {
        do {
            $code = 'CLM-'.Str::upper(Str::random(10));
        } while (VoucherClaim::query()->where('claim_code', $code)->exists());

        return $code;
    }
}
