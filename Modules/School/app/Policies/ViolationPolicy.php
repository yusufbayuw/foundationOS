<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\Violation;

class ViolationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Violation');
    }

    public function view(AuthUser $authUser, Violation $violation): bool
    {
        return $authUser->can('View:Violation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Violation');
    }

    public function update(AuthUser $authUser, Violation $violation): bool
    {
        return $authUser->can('Update:Violation');
    }

    public function delete(AuthUser $authUser, Violation $violation): bool
    {
        return $authUser->can('Delete:Violation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Violation');
    }

    public function restore(AuthUser $authUser, Violation $violation): bool
    {
        return $authUser->can('Restore:Violation');
    }

    public function forceDelete(AuthUser $authUser, Violation $violation): bool
    {
        return $authUser->can('ForceDelete:Violation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Violation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Violation');
    }

    public function replicate(AuthUser $authUser, Violation $violation): bool
    {
        return $authUser->can('Replicate:Violation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Violation');
    }
}
