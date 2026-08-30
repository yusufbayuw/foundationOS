<?php

declare(strict_types=1);

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cms\Models\HomepageSection;

class HomepageSectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HomepageSection');
    }

    public function view(AuthUser $authUser, HomepageSection $homepageSection): bool
    {
        return $authUser->can('View:HomepageSection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HomepageSection');
    }

    public function update(AuthUser $authUser, HomepageSection $homepageSection): bool
    {
        return $authUser->can('Update:HomepageSection');
    }

    public function delete(AuthUser $authUser, HomepageSection $homepageSection): bool
    {
        return $authUser->can('Delete:HomepageSection');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HomepageSection');
    }

    public function restore(AuthUser $authUser, HomepageSection $homepageSection): bool
    {
        return $authUser->can('Restore:HomepageSection');
    }

    public function forceDelete(AuthUser $authUser, HomepageSection $homepageSection): bool
    {
        return $authUser->can('ForceDelete:HomepageSection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HomepageSection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HomepageSection');
    }

    public function replicate(AuthUser $authUser, HomepageSection $homepageSection): bool
    {
        return $authUser->can('Replicate:HomepageSection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HomepageSection');
    }
}
