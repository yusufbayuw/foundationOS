<?php

declare(strict_types=1);

namespace Modules\Counseling\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Counseling\Models\Counselor;

class CounselorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Counselor');
    }

    public function view(AuthUser $authUser, Counselor $counselor): bool
    {
        return $authUser->can('View:Counselor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Counselor');
    }

    public function update(AuthUser $authUser, Counselor $counselor): bool
    {
        return $authUser->can('Update:Counselor');
    }

    public function delete(AuthUser $authUser, Counselor $counselor): bool
    {
        return $authUser->can('Delete:Counselor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Counselor');
    }

    public function restore(AuthUser $authUser, Counselor $counselor): bool
    {
        return $authUser->can('Restore:Counselor');
    }

    public function forceDelete(AuthUser $authUser, Counselor $counselor): bool
    {
        return $authUser->can('ForceDelete:Counselor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Counselor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Counselor');
    }

    public function replicate(AuthUser $authUser, Counselor $counselor): bool
    {
        return $authUser->can('Replicate:Counselor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Counselor');
    }
}
