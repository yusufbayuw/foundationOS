<?php

declare(strict_types=1);

namespace Modules\Consulting\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Consulting\Models\ConsultantTimesheet;

class ConsultantTimesheetPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConsultantTimesheet');
    }

    public function view(AuthUser $authUser, ConsultantTimesheet $consultantTimesheet): bool
    {
        return $authUser->can('View:ConsultantTimesheet');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConsultantTimesheet');
    }

    public function update(AuthUser $authUser, ConsultantTimesheet $consultantTimesheet): bool
    {
        return $authUser->can('Update:ConsultantTimesheet');
    }

    public function delete(AuthUser $authUser, ConsultantTimesheet $consultantTimesheet): bool
    {
        return $authUser->can('Delete:ConsultantTimesheet');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ConsultantTimesheet');
    }

    public function restore(AuthUser $authUser, ConsultantTimesheet $consultantTimesheet): bool
    {
        return $authUser->can('Restore:ConsultantTimesheet');
    }

    public function forceDelete(AuthUser $authUser, ConsultantTimesheet $consultantTimesheet): bool
    {
        return $authUser->can('ForceDelete:ConsultantTimesheet');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConsultantTimesheet');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConsultantTimesheet');
    }

    public function replicate(AuthUser $authUser, ConsultantTimesheet $consultantTimesheet): bool
    {
        return $authUser->can('Replicate:ConsultantTimesheet');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConsultantTimesheet');
    }
}
