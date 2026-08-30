<?php

declare(strict_types=1);

namespace Modules\Property\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Property\Models\CommercialTenant;

class CommercialTenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommercialTenant');
    }

    public function view(AuthUser $authUser, CommercialTenant $commercialTenant): bool
    {
        return $authUser->can('View:CommercialTenant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommercialTenant');
    }

    public function update(AuthUser $authUser, CommercialTenant $commercialTenant): bool
    {
        return $authUser->can('Update:CommercialTenant');
    }

    public function delete(AuthUser $authUser, CommercialTenant $commercialTenant): bool
    {
        return $authUser->can('Delete:CommercialTenant');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CommercialTenant');
    }

    public function restore(AuthUser $authUser, CommercialTenant $commercialTenant): bool
    {
        return $authUser->can('Restore:CommercialTenant');
    }

    public function forceDelete(AuthUser $authUser, CommercialTenant $commercialTenant): bool
    {
        return $authUser->can('ForceDelete:CommercialTenant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CommercialTenant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CommercialTenant');
    }

    public function replicate(AuthUser $authUser, CommercialTenant $commercialTenant): bool
    {
        return $authUser->can('Replicate:CommercialTenant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CommercialTenant');
    }
}
