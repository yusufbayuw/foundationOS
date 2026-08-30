<?php

declare(strict_types=1);

namespace Modules\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Asset\Models\AssetInsurance;

class AssetInsurancePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AssetInsurance');
    }

    public function view(AuthUser $authUser, AssetInsurance $assetInsurance): bool
    {
        return $authUser->can('View:AssetInsurance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AssetInsurance');
    }

    public function update(AuthUser $authUser, AssetInsurance $assetInsurance): bool
    {
        return $authUser->can('Update:AssetInsurance');
    }

    public function delete(AuthUser $authUser, AssetInsurance $assetInsurance): bool
    {
        return $authUser->can('Delete:AssetInsurance');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AssetInsurance');
    }

    public function restore(AuthUser $authUser, AssetInsurance $assetInsurance): bool
    {
        return $authUser->can('Restore:AssetInsurance');
    }

    public function forceDelete(AuthUser $authUser, AssetInsurance $assetInsurance): bool
    {
        return $authUser->can('ForceDelete:AssetInsurance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AssetInsurance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AssetInsurance');
    }

    public function replicate(AuthUser $authUser, AssetInsurance $assetInsurance): bool
    {
        return $authUser->can('Replicate:AssetInsurance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AssetInsurance');
    }
}
