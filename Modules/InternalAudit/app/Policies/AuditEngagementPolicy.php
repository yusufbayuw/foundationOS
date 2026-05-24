<?php

declare(strict_types=1);

namespace Modules\InternalAudit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\InternalAudit\Models\AuditEngagement;

class AuditEngagementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AuditEngagement');
    }

    public function view(AuthUser $authUser, AuditEngagement $auditEngagement): bool
    {
        return $authUser->can('View:AuditEngagement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AuditEngagement');
    }

    public function update(AuthUser $authUser, AuditEngagement $auditEngagement): bool
    {
        return $authUser->can('Update:AuditEngagement');
    }

    public function delete(AuthUser $authUser, AuditEngagement $auditEngagement): bool
    {
        return $authUser->can('Delete:AuditEngagement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AuditEngagement');
    }

    public function restore(AuthUser $authUser, AuditEngagement $auditEngagement): bool
    {
        return $authUser->can('Restore:AuditEngagement');
    }

    public function forceDelete(AuthUser $authUser, AuditEngagement $auditEngagement): bool
    {
        return $authUser->can('ForceDelete:AuditEngagement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AuditEngagement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AuditEngagement');
    }

    public function replicate(AuthUser $authUser, AuditEngagement $auditEngagement): bool
    {
        return $authUser->can('Replicate:AuditEngagement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AuditEngagement');
    }
}
