<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\Curriculum;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurriculumPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Curriculum');
    }

    public function view(AuthUser $authUser, Curriculum $curriculum): bool
    {
        return $authUser->can('View:Curriculum');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Curriculum');
    }

    public function update(AuthUser $authUser, Curriculum $curriculum): bool
    {
        return $authUser->can('Update:Curriculum');
    }

    public function delete(AuthUser $authUser, Curriculum $curriculum): bool
    {
        return $authUser->can('Delete:Curriculum');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Curriculum');
    }

    public function restore(AuthUser $authUser, Curriculum $curriculum): bool
    {
        return $authUser->can('Restore:Curriculum');
    }

    public function forceDelete(AuthUser $authUser, Curriculum $curriculum): bool
    {
        return $authUser->can('ForceDelete:Curriculum');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Curriculum');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Curriculum');
    }

    public function replicate(AuthUser $authUser, Curriculum $curriculum): bool
    {
        return $authUser->can('Replicate:Curriculum');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Curriculum');
    }

}