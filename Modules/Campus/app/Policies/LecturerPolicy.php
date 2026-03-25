<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\Lecturer;
use Illuminate\Auth\Access\HandlesAuthorization;

class LecturerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Lecturer');
    }

    public function view(AuthUser $authUser, Lecturer $lecturer): bool
    {
        return $authUser->can('View:Lecturer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Lecturer');
    }

    public function update(AuthUser $authUser, Lecturer $lecturer): bool
    {
        return $authUser->can('Update:Lecturer');
    }

    public function delete(AuthUser $authUser, Lecturer $lecturer): bool
    {
        return $authUser->can('Delete:Lecturer');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Lecturer');
    }

    public function restore(AuthUser $authUser, Lecturer $lecturer): bool
    {
        return $authUser->can('Restore:Lecturer');
    }

    public function forceDelete(AuthUser $authUser, Lecturer $lecturer): bool
    {
        return $authUser->can('ForceDelete:Lecturer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Lecturer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Lecturer');
    }

    public function replicate(AuthUser $authUser, Lecturer $lecturer): bool
    {
        return $authUser->can('Replicate:Lecturer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Lecturer');
    }

}