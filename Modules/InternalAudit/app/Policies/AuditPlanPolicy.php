<?php

declare(strict_types=1);

namespace Modules\InternalAudit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\InternalAudit\Models\AuditPlan;

class AuditPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AuditPlan');
    }

    public function view(AuthUser $authUser, AuditPlan $auditPlan): bool
    {
        return $authUser->can('View:AuditPlan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AuditPlan');
    }

    public function update(AuthUser $authUser, AuditPlan $auditPlan): bool
    {
        return $authUser->can('Update:AuditPlan');
    }

    public function delete(AuthUser $authUser, AuditPlan $auditPlan): bool
    {
        return $authUser->can('Delete:AuditPlan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AuditPlan');
    }

    public function restore(AuthUser $authUser, AuditPlan $auditPlan): bool
    {
        return $authUser->can('Restore:AuditPlan');
    }

    public function forceDelete(AuthUser $authUser, AuditPlan $auditPlan): bool
    {
        return $authUser->can('ForceDelete:AuditPlan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AuditPlan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AuditPlan');
    }

    public function replicate(AuthUser $authUser, AuditPlan $auditPlan): bool
    {
        return $authUser->can('Replicate:AuditPlan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AuditPlan');
    }
}
