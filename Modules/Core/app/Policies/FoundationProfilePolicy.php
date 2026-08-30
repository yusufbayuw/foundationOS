<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\FoundationProfile;

class FoundationProfilePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FoundationProfile');
    }

    public function view(AuthUser $authUser, FoundationProfile $foundationProfile): bool
    {
        return $authUser->can('View:FoundationProfile');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FoundationProfile');
    }

    public function update(AuthUser $authUser, FoundationProfile $foundationProfile): bool
    {
        return $authUser->can('Update:FoundationProfile');
    }

    public function delete(AuthUser $authUser, FoundationProfile $foundationProfile): bool
    {
        return $authUser->can('Delete:FoundationProfile');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FoundationProfile');
    }

    public function restore(AuthUser $authUser, FoundationProfile $foundationProfile): bool
    {
        return $authUser->can('Restore:FoundationProfile');
    }

    public function forceDelete(AuthUser $authUser, FoundationProfile $foundationProfile): bool
    {
        return $authUser->can('ForceDelete:FoundationProfile');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FoundationProfile');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FoundationProfile');
    }

    public function replicate(AuthUser $authUser, FoundationProfile $foundationProfile): bool
    {
        return $authUser->can('Replicate:FoundationProfile');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FoundationProfile');
    }
}
