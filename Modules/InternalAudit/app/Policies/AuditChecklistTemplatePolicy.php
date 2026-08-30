<?php

declare(strict_types=1);

namespace Modules\InternalAudit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\InternalAudit\Models\AuditChecklistTemplate;

class AuditChecklistTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AuditChecklistTemplate');
    }

    public function view(AuthUser $authUser, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $authUser->can('View:AuditChecklistTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AuditChecklistTemplate');
    }

    public function update(AuthUser $authUser, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $authUser->can('Update:AuditChecklistTemplate');
    }

    public function delete(AuthUser $authUser, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $authUser->can('Delete:AuditChecklistTemplate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AuditChecklistTemplate');
    }

    public function restore(AuthUser $authUser, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $authUser->can('Restore:AuditChecklistTemplate');
    }

    public function forceDelete(AuthUser $authUser, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $authUser->can('ForceDelete:AuditChecklistTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AuditChecklistTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AuditChecklistTemplate');
    }

    public function replicate(AuthUser $authUser, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $authUser->can('Replicate:AuditChecklistTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AuditChecklistTemplate');
    }
}
