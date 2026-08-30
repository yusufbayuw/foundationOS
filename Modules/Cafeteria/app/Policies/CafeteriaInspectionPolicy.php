<?php

declare(strict_types=1);

namespace Modules\Cafeteria\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cafeteria\Models\CafeteriaInspection;

class CafeteriaInspectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CafeteriaInspection');
    }

    public function view(AuthUser $authUser, CafeteriaInspection $cafeteriaInspection): bool
    {
        return $authUser->can('View:CafeteriaInspection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CafeteriaInspection');
    }

    public function update(AuthUser $authUser, CafeteriaInspection $cafeteriaInspection): bool
    {
        return $authUser->can('Update:CafeteriaInspection');
    }

    public function delete(AuthUser $authUser, CafeteriaInspection $cafeteriaInspection): bool
    {
        return $authUser->can('Delete:CafeteriaInspection');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CafeteriaInspection');
    }

    public function restore(AuthUser $authUser, CafeteriaInspection $cafeteriaInspection): bool
    {
        return $authUser->can('Restore:CafeteriaInspection');
    }

    public function forceDelete(AuthUser $authUser, CafeteriaInspection $cafeteriaInspection): bool
    {
        return $authUser->can('ForceDelete:CafeteriaInspection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CafeteriaInspection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CafeteriaInspection');
    }

    public function replicate(AuthUser $authUser, CafeteriaInspection $cafeteriaInspection): bool
    {
        return $authUser->can('Replicate:CafeteriaInspection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CafeteriaInspection');
    }
}
