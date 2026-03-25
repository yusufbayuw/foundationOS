<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\OrganizationSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationSettingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OrganizationSetting');
    }

    public function view(AuthUser $authUser, OrganizationSetting $organizationSetting): bool
    {
        return $authUser->can('View:OrganizationSetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OrganizationSetting');
    }

    public function update(AuthUser $authUser, OrganizationSetting $organizationSetting): bool
    {
        return $authUser->can('Update:OrganizationSetting');
    }

    public function delete(AuthUser $authUser, OrganizationSetting $organizationSetting): bool
    {
        return $authUser->can('Delete:OrganizationSetting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:OrganizationSetting');
    }

    public function restore(AuthUser $authUser, OrganizationSetting $organizationSetting): bool
    {
        return $authUser->can('Restore:OrganizationSetting');
    }

    public function forceDelete(AuthUser $authUser, OrganizationSetting $organizationSetting): bool
    {
        return $authUser->can('ForceDelete:OrganizationSetting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OrganizationSetting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OrganizationSetting');
    }

    public function replicate(AuthUser $authUser, OrganizationSetting $organizationSetting): bool
    {
        return $authUser->can('Replicate:OrganizationSetting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OrganizationSetting');
    }

}