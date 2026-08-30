<?php

declare(strict_types=1);

namespace Modules\Risk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Risk\Models\RiskOwner;

class RiskOwnerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RiskOwner');
    }

    public function view(AuthUser $authUser, RiskOwner $riskOwner): bool
    {
        return $authUser->can('View:RiskOwner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RiskOwner');
    }

    public function update(AuthUser $authUser, RiskOwner $riskOwner): bool
    {
        return $authUser->can('Update:RiskOwner');
    }

    public function delete(AuthUser $authUser, RiskOwner $riskOwner): bool
    {
        return $authUser->can('Delete:RiskOwner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RiskOwner');
    }

    public function restore(AuthUser $authUser, RiskOwner $riskOwner): bool
    {
        return $authUser->can('Restore:RiskOwner');
    }

    public function forceDelete(AuthUser $authUser, RiskOwner $riskOwner): bool
    {
        return $authUser->can('ForceDelete:RiskOwner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RiskOwner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RiskOwner');
    }

    public function replicate(AuthUser $authUser, RiskOwner $riskOwner): bool
    {
        return $authUser->can('Replicate:RiskOwner');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RiskOwner');
    }
}
