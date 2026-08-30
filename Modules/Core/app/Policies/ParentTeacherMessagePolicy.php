<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\ParentTeacherMessage;

class ParentTeacherMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParentTeacherMessage');
    }

    public function view(AuthUser $authUser, ParentTeacherMessage $parentTeacherMessage): bool
    {
        return $authUser->can('View:ParentTeacherMessage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParentTeacherMessage');
    }

    public function update(AuthUser $authUser, ParentTeacherMessage $parentTeacherMessage): bool
    {
        return $authUser->can('Update:ParentTeacherMessage');
    }

    public function delete(AuthUser $authUser, ParentTeacherMessage $parentTeacherMessage): bool
    {
        return $authUser->can('Delete:ParentTeacherMessage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParentTeacherMessage');
    }

    public function restore(AuthUser $authUser, ParentTeacherMessage $parentTeacherMessage): bool
    {
        return $authUser->can('Restore:ParentTeacherMessage');
    }

    public function forceDelete(AuthUser $authUser, ParentTeacherMessage $parentTeacherMessage): bool
    {
        return $authUser->can('ForceDelete:ParentTeacherMessage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParentTeacherMessage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParentTeacherMessage');
    }

    public function replicate(AuthUser $authUser, ParentTeacherMessage $parentTeacherMessage): bool
    {
        return $authUser->can('Replicate:ParentTeacherMessage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParentTeacherMessage');
    }
}
