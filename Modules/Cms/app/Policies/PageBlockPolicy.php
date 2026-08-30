<?php

declare(strict_types=1);

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cms\Models\PageBlock;

class PageBlockPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PageBlock');
    }

    public function view(AuthUser $authUser, PageBlock $pageBlock): bool
    {
        return $authUser->can('View:PageBlock');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PageBlock');
    }

    public function update(AuthUser $authUser, PageBlock $pageBlock): bool
    {
        return $authUser->can('Update:PageBlock');
    }

    public function delete(AuthUser $authUser, PageBlock $pageBlock): bool
    {
        return $authUser->can('Delete:PageBlock');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PageBlock');
    }

    public function restore(AuthUser $authUser, PageBlock $pageBlock): bool
    {
        return $authUser->can('Restore:PageBlock');
    }

    public function forceDelete(AuthUser $authUser, PageBlock $pageBlock): bool
    {
        return $authUser->can('ForceDelete:PageBlock');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PageBlock');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PageBlock');
    }

    public function replicate(AuthUser $authUser, PageBlock $pageBlock): bool
    {
        return $authUser->can('Replicate:PageBlock');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PageBlock');
    }
}
