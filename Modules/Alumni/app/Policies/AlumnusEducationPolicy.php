<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\AlumnusEducation;

class AlumnusEducationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AlumnusEducation');
    }

    public function view(AuthUser $authUser, AlumnusEducation $alumnusEducation): bool
    {
        return $authUser->can('View:AlumnusEducation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AlumnusEducation');
    }

    public function update(AuthUser $authUser, AlumnusEducation $alumnusEducation): bool
    {
        return $authUser->can('Update:AlumnusEducation');
    }

    public function delete(AuthUser $authUser, AlumnusEducation $alumnusEducation): bool
    {
        return $authUser->can('Delete:AlumnusEducation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AlumnusEducation');
    }

    public function restore(AuthUser $authUser, AlumnusEducation $alumnusEducation): bool
    {
        return $authUser->can('Restore:AlumnusEducation');
    }

    public function forceDelete(AuthUser $authUser, AlumnusEducation $alumnusEducation): bool
    {
        return $authUser->can('ForceDelete:AlumnusEducation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AlumnusEducation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AlumnusEducation');
    }

    public function replicate(AuthUser $authUser, AlumnusEducation $alumnusEducation): bool
    {
        return $authUser->can('Replicate:AlumnusEducation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AlumnusEducation');
    }
}
