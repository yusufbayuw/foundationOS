<?php

declare(strict_types=1);

namespace Modules\IsoCompliance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\IsoCompliance\Models\InformationAsset;

class InformationAssetPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InformationAsset');
    }

    public function view(AuthUser $authUser, InformationAsset $informationAsset): bool
    {
        return $authUser->can('View:InformationAsset');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InformationAsset');
    }

    public function update(AuthUser $authUser, InformationAsset $informationAsset): bool
    {
        return $authUser->can('Update:InformationAsset');
    }

    public function delete(AuthUser $authUser, InformationAsset $informationAsset): bool
    {
        return $authUser->can('Delete:InformationAsset');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InformationAsset');
    }

    public function restore(AuthUser $authUser, InformationAsset $informationAsset): bool
    {
        return $authUser->can('Restore:InformationAsset');
    }

    public function forceDelete(AuthUser $authUser, InformationAsset $informationAsset): bool
    {
        return $authUser->can('ForceDelete:InformationAsset');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InformationAsset');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InformationAsset');
    }

    public function replicate(AuthUser $authUser, InformationAsset $informationAsset): bool
    {
        return $authUser->can('Replicate:InformationAsset');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InformationAsset');
    }
}
