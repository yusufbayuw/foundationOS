<?php

declare(strict_types=1);

namespace Modules\IsoCompliance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\IsoCompliance\Models\ControlImplementation;

class ControlImplementationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ControlImplementation');
    }

    public function view(AuthUser $authUser, ControlImplementation $controlImplementation): bool
    {
        return $authUser->can('View:ControlImplementation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ControlImplementation');
    }

    public function update(AuthUser $authUser, ControlImplementation $controlImplementation): bool
    {
        return $authUser->can('Update:ControlImplementation');
    }

    public function delete(AuthUser $authUser, ControlImplementation $controlImplementation): bool
    {
        return $authUser->can('Delete:ControlImplementation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ControlImplementation');
    }

    public function restore(AuthUser $authUser, ControlImplementation $controlImplementation): bool
    {
        return $authUser->can('Restore:ControlImplementation');
    }

    public function forceDelete(AuthUser $authUser, ControlImplementation $controlImplementation): bool
    {
        return $authUser->can('ForceDelete:ControlImplementation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ControlImplementation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ControlImplementation');
    }

    public function replicate(AuthUser $authUser, ControlImplementation $controlImplementation): bool
    {
        return $authUser->can('Replicate:ControlImplementation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ControlImplementation');
    }
}
