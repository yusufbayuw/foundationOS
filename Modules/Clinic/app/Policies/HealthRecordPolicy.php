<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use App\Scopes\TenantScope;
use Modules\Clinic\Models\HealthRecord;

class HealthRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HealthRecord');
    }

    public function view(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        $activeTenantId = TenantScope::resolveTenantId();

        return ($activeTenantId === null || (int) $activeTenantId === (int) $healthRecord->tenant_id)
            && $authUser->can('View:HealthRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HealthRecord');
    }

    public function update(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('Update:HealthRecord') && $this->view($authUser, $healthRecord);
    }

    public function delete(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('Delete:HealthRecord') && $this->view($authUser, $healthRecord);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function restore(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('Restore:HealthRecord') && $this->view($authUser, $healthRecord);
    }

    public function forceDelete(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return false;
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function replicate(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HealthRecord');
    }
}
