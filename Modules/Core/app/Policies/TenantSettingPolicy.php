<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\TenantSetting;

class TenantSettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TenantSetting');
    }

    public function view(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('View:TenantSetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TenantSetting');
    }

    public function update(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('Update:TenantSetting');
    }

    public function delete(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('Delete:TenantSetting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TenantSetting');
    }

    public function restore(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('Restore:TenantSetting');
    }

    public function forceDelete(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('ForceDelete:TenantSetting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TenantSetting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TenantSetting');
    }

    public function replicate(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('Replicate:TenantSetting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TenantSetting');
    }
}
