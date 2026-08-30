<?php

declare(strict_types=1);

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cms\Models\CmsMedia;

class CmsMediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CmsMedia');
    }

    public function view(AuthUser $authUser, CmsMedia $cmsMedia): bool
    {
        return $authUser->can('View:CmsMedia');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CmsMedia');
    }

    public function update(AuthUser $authUser, CmsMedia $cmsMedia): bool
    {
        return $authUser->can('Update:CmsMedia');
    }

    public function delete(AuthUser $authUser, CmsMedia $cmsMedia): bool
    {
        return $authUser->can('Delete:CmsMedia');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CmsMedia');
    }

    public function restore(AuthUser $authUser, CmsMedia $cmsMedia): bool
    {
        return $authUser->can('Restore:CmsMedia');
    }

    public function forceDelete(AuthUser $authUser, CmsMedia $cmsMedia): bool
    {
        return $authUser->can('ForceDelete:CmsMedia');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CmsMedia');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CmsMedia');
    }

    public function replicate(AuthUser $authUser, CmsMedia $cmsMedia): bool
    {
        return $authUser->can('Replicate:CmsMedia');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CmsMedia');
    }
}
