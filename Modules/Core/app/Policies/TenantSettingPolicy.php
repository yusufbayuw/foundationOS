<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Policies\Concerns\AuthorizesTenantScopedRecord;

class TenantSettingPolicy
{
    use AuthorizesTenantScopedRecord;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TenantSetting');
    }

    public function view(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('View:TenantSetting')
            && $this->belongsToActiveTenant($authUser, $tenantSetting);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TenantSetting');
    }

    public function update(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('Update:TenantSetting')
            && $this->belongsToActiveTenant($authUser, $tenantSetting);
    }

    public function delete(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('Delete:TenantSetting')
            && $this->belongsToActiveTenant($authUser, $tenantSetting);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TenantSetting');
    }

    public function restore(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('Restore:TenantSetting')
            && $this->belongsToActiveTenant($authUser, $tenantSetting);
    }

    public function forceDelete(AuthUser $authUser, TenantSetting $tenantSetting): bool
    {
        return $authUser->can('ForceDelete:TenantSetting')
            && $this->belongsToActiveTenant($authUser, $tenantSetting);
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
        return $authUser->can('Replicate:TenantSetting')
            && $this->belongsToActiveTenant($authUser, $tenantSetting);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TenantSetting');
    }
}
