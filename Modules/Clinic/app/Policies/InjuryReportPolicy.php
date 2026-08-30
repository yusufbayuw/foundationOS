<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Clinic\Models\InjuryReport;

class InjuryReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InjuryReport');
    }

    public function view(AuthUser $authUser, InjuryReport $injuryReport): bool
    {
        return $authUser->can('View:InjuryReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InjuryReport');
    }

    public function update(AuthUser $authUser, InjuryReport $injuryReport): bool
    {
        return $authUser->can('Update:InjuryReport');
    }

    public function delete(AuthUser $authUser, InjuryReport $injuryReport): bool
    {
        return $authUser->can('Delete:InjuryReport');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InjuryReport');
    }

    public function restore(AuthUser $authUser, InjuryReport $injuryReport): bool
    {
        return $authUser->can('Restore:InjuryReport');
    }

    public function forceDelete(AuthUser $authUser, InjuryReport $injuryReport): bool
    {
        return $authUser->can('ForceDelete:InjuryReport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InjuryReport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InjuryReport');
    }

    public function replicate(AuthUser $authUser, InjuryReport $injuryReport): bool
    {
        return $authUser->can('Replicate:InjuryReport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InjuryReport');
    }
}
