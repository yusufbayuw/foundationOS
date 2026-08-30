<?php

declare(strict_types=1);

namespace Modules\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Asset\Models\AssetDepreciation;

class AssetDepreciationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AssetDepreciation');
    }

    public function view(AuthUser $authUser, AssetDepreciation $assetDepreciation): bool
    {
        return $authUser->can('View:AssetDepreciation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AssetDepreciation');
    }

    public function update(AuthUser $authUser, AssetDepreciation $assetDepreciation): bool
    {
        return $authUser->can('Update:AssetDepreciation');
    }

    public function delete(AuthUser $authUser, AssetDepreciation $assetDepreciation): bool
    {
        return $authUser->can('Delete:AssetDepreciation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AssetDepreciation');
    }

    public function restore(AuthUser $authUser, AssetDepreciation $assetDepreciation): bool
    {
        return $authUser->can('Restore:AssetDepreciation');
    }

    public function forceDelete(AuthUser $authUser, AssetDepreciation $assetDepreciation): bool
    {
        return $authUser->can('ForceDelete:AssetDepreciation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AssetDepreciation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AssetDepreciation');
    }

    public function replicate(AuthUser $authUser, AssetDepreciation $assetDepreciation): bool
    {
        return $authUser->can('Replicate:AssetDepreciation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AssetDepreciation');
    }
}
