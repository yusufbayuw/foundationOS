<?php

declare(strict_types=1);

namespace Modules\IsoCompliance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\IsoCompliance\Models\IsoEvidence;

class IsoEvidencePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IsoEvidence');
    }

    public function view(AuthUser $authUser, IsoEvidence $isoEvidence): bool
    {
        return $authUser->can('View:IsoEvidence');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IsoEvidence');
    }

    public function update(AuthUser $authUser, IsoEvidence $isoEvidence): bool
    {
        return $authUser->can('Update:IsoEvidence');
    }

    public function delete(AuthUser $authUser, IsoEvidence $isoEvidence): bool
    {
        return $authUser->can('Delete:IsoEvidence');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IsoEvidence');
    }

    public function restore(AuthUser $authUser, IsoEvidence $isoEvidence): bool
    {
        return $authUser->can('Restore:IsoEvidence');
    }

    public function forceDelete(AuthUser $authUser, IsoEvidence $isoEvidence): bool
    {
        return $authUser->can('ForceDelete:IsoEvidence');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IsoEvidence');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IsoEvidence');
    }

    public function replicate(AuthUser $authUser, IsoEvidence $isoEvidence): bool
    {
        return $authUser->can('Replicate:IsoEvidence');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IsoEvidence');
    }
}
