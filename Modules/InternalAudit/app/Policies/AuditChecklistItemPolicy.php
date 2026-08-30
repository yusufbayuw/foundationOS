<?php

declare(strict_types=1);

namespace Modules\InternalAudit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\InternalAudit\Models\AuditChecklistItem;

class AuditChecklistItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AuditChecklistItem');
    }

    public function view(AuthUser $authUser, AuditChecklistItem $auditChecklistItem): bool
    {
        return $authUser->can('View:AuditChecklistItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AuditChecklistItem');
    }

    public function update(AuthUser $authUser, AuditChecklistItem $auditChecklistItem): bool
    {
        return $authUser->can('Update:AuditChecklistItem');
    }

    public function delete(AuthUser $authUser, AuditChecklistItem $auditChecklistItem): bool
    {
        return $authUser->can('Delete:AuditChecklistItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AuditChecklistItem');
    }

    public function restore(AuthUser $authUser, AuditChecklistItem $auditChecklistItem): bool
    {
        return $authUser->can('Restore:AuditChecklistItem');
    }

    public function forceDelete(AuthUser $authUser, AuditChecklistItem $auditChecklistItem): bool
    {
        return $authUser->can('ForceDelete:AuditChecklistItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AuditChecklistItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AuditChecklistItem');
    }

    public function replicate(AuthUser $authUser, AuditChecklistItem $auditChecklistItem): bool
    {
        return $authUser->can('Replicate:AuditChecklistItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AuditChecklistItem');
    }
}
