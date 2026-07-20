<?php

declare(strict_types=1);

namespace Modules\Voucher\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Voucher\Models\VoucherClaim;

class VoucherClaimPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VoucherClaim');
    }

    public function view(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $authUser->can('View:VoucherClaim');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:VoucherClaim');
    }
}
