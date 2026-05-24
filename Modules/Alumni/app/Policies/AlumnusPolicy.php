<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\Alumnus;

class AlumnusPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Alumnus');
    }

    public function view(AuthUser $authUser, Alumnus $alumnus): bool
    {
        return $authUser->can('View:Alumnus');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Alumnus');
    }

    public function update(AuthUser $authUser, Alumnus $alumnus): bool
    {
        return $authUser->can('Update:Alumnus');
    }

    public function delete(AuthUser $authUser, Alumnus $alumnus): bool
    {
        return $authUser->can('Delete:Alumnus');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Alumnus');
    }

    public function restore(AuthUser $authUser, Alumnus $alumnus): bool
    {
        return $authUser->can('Restore:Alumnus');
    }

    public function forceDelete(AuthUser $authUser, Alumnus $alumnus): bool
    {
        return $authUser->can('ForceDelete:Alumnus');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Alumnus');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Alumnus');
    }

    public function replicate(AuthUser $authUser, Alumnus $alumnus): bool
    {
        return $authUser->can('Replicate:Alumnus');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Alumnus');
    }
}
