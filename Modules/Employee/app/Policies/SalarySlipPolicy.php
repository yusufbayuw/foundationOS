<?php

declare(strict_types=1);

namespace Modules\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Employee\Models\SalarySlip;

class SalarySlipPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalarySlip');
    }

    public function view(AuthUser $authUser, SalarySlip $salarySlip): bool
    {
        return $authUser->can('View:SalarySlip');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalarySlip');
    }

    public function update(AuthUser $authUser, SalarySlip $salarySlip): bool
    {
        return $authUser->can('Update:SalarySlip');
    }

    public function delete(AuthUser $authUser, SalarySlip $salarySlip): bool
    {
        return $authUser->can('Delete:SalarySlip');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SalarySlip');
    }

    public function restore(AuthUser $authUser, SalarySlip $salarySlip): bool
    {
        return $authUser->can('Restore:SalarySlip');
    }

    public function forceDelete(AuthUser $authUser, SalarySlip $salarySlip): bool
    {
        return $authUser->can('ForceDelete:SalarySlip');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalarySlip');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalarySlip');
    }

    public function replicate(AuthUser $authUser, SalarySlip $salarySlip): bool
    {
        return $authUser->can('Replicate:SalarySlip');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalarySlip');
    }
}
