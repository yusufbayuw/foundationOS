<?php

declare(strict_types=1);

namespace Modules\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Employee\Models\SalarySlipComponent;

class SalarySlipComponentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalarySlipComponent');
    }

    public function view(AuthUser $authUser, SalarySlipComponent $salarySlipComponent): bool
    {
        return $authUser->can('View:SalarySlipComponent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalarySlipComponent');
    }

    public function update(AuthUser $authUser, SalarySlipComponent $salarySlipComponent): bool
    {
        return $authUser->can('Update:SalarySlipComponent');
    }

    public function delete(AuthUser $authUser, SalarySlipComponent $salarySlipComponent): bool
    {
        return $authUser->can('Delete:SalarySlipComponent');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SalarySlipComponent');
    }

    public function restore(AuthUser $authUser, SalarySlipComponent $salarySlipComponent): bool
    {
        return $authUser->can('Restore:SalarySlipComponent');
    }

    public function forceDelete(AuthUser $authUser, SalarySlipComponent $salarySlipComponent): bool
    {
        return $authUser->can('ForceDelete:SalarySlipComponent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalarySlipComponent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalarySlipComponent');
    }

    public function replicate(AuthUser $authUser, SalarySlipComponent $salarySlipComponent): bool
    {
        return $authUser->can('Replicate:SalarySlipComponent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalarySlipComponent');
    }
}
