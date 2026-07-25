<?php

namespace App\Policies;

use App\Models\Voucher;
use Illuminate\Foundation\Auth\User as AuthUser;

class VoucherPolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, Voucher $voucher): bool
    {
        return true;
    }

    public function claim(AuthUser $authUser, Voucher $voucher): bool
    {
        return true;
    }
}
