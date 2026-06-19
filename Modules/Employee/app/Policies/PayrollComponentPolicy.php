<?php

declare(strict_types=1);

namespace Modules\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Employee\Models\PayrollComponent;

class PayrollComponentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PayrollComponent');
    }

    public function view(AuthUser $authUser, PayrollComponent $payrollComponent): bool
    {
        return $authUser->can('View:PayrollComponent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PayrollComponent');
    }

    public function update(AuthUser $authUser, PayrollComponent $payrollComponent): bool
    {
        return $authUser->can('Update:PayrollComponent');
    }

    public function delete(AuthUser $authUser, PayrollComponent $payrollComponent): bool
    {
        return $authUser->can('Delete:PayrollComponent');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PayrollComponent');
    }

    public function restore(AuthUser $authUser, PayrollComponent $payrollComponent): bool
    {
        return $authUser->can('Restore:PayrollComponent');
    }

    public function forceDelete(AuthUser $authUser, PayrollComponent $payrollComponent): bool
    {
        return $authUser->can('ForceDelete:PayrollComponent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PayrollComponent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PayrollComponent');
    }

    public function replicate(AuthUser $authUser, PayrollComponent $payrollComponent): bool
    {
        return $authUser->can('Replicate:PayrollComponent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PayrollComponent');
    }
}
