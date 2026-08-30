<?php

declare(strict_types=1);

namespace Modules\InternalAudit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\InternalAudit\Models\FindingEvidence;

class FindingEvidencePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FindingEvidence');
    }

    public function view(AuthUser $authUser, FindingEvidence $findingEvidence): bool
    {
        return $authUser->can('View:FindingEvidence');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FindingEvidence');
    }

    public function update(AuthUser $authUser, FindingEvidence $findingEvidence): bool
    {
        return $authUser->can('Update:FindingEvidence');
    }

    public function delete(AuthUser $authUser, FindingEvidence $findingEvidence): bool
    {
        return $authUser->can('Delete:FindingEvidence');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FindingEvidence');
    }

    public function restore(AuthUser $authUser, FindingEvidence $findingEvidence): bool
    {
        return $authUser->can('Restore:FindingEvidence');
    }

    public function forceDelete(AuthUser $authUser, FindingEvidence $findingEvidence): bool
    {
        return $authUser->can('ForceDelete:FindingEvidence');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FindingEvidence');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FindingEvidence');
    }

    public function replicate(AuthUser $authUser, FindingEvidence $findingEvidence): bool
    {
        return $authUser->can('Replicate:FindingEvidence');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FindingEvidence');
    }
}
