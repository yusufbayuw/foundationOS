<?php

namespace App\Policies;

use App\Models\VoucherClaim;
use Illuminate\Foundation\Auth\User as AuthUser;

class VoucherClaimPolicy
{
    public function redeem(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $voucherClaim->user_id === $authUser->getAuthIdentifier();
    }
}
