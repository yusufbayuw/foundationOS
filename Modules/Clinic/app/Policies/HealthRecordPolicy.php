<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
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
        return $authUser->can('View:HealthRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HealthRecord');
    }

    public function update(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('Update:HealthRecord');
    }

    public function delete(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('Delete:HealthRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HealthRecord');
    }

    public function restore(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('Restore:HealthRecord');
    }

    public function forceDelete(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('ForceDelete:HealthRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HealthRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HealthRecord');
    }

    public function replicate(AuthUser $authUser, HealthRecord $healthRecord): bool
    {
        return $authUser->can('Replicate:HealthRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HealthRecord');
    }
}
