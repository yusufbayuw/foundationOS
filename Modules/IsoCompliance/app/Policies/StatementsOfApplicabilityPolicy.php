<?php

declare(strict_types=1);

namespace Modules\IsoCompliance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\IsoCompliance\Models\StatementsOfApplicability;

class StatementsOfApplicabilityPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StatementsOfApplicability');
    }

    public function view(AuthUser $authUser, StatementsOfApplicability $statementsOfApplicability): bool
    {
        return $authUser->can('View:StatementsOfApplicability');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StatementsOfApplicability');
    }

    public function update(AuthUser $authUser, StatementsOfApplicability $statementsOfApplicability): bool
    {
        return $authUser->can('Update:StatementsOfApplicability');
    }

    public function delete(AuthUser $authUser, StatementsOfApplicability $statementsOfApplicability): bool
    {
        return $authUser->can('Delete:StatementsOfApplicability');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StatementsOfApplicability');
    }

    public function restore(AuthUser $authUser, StatementsOfApplicability $statementsOfApplicability): bool
    {
        return $authUser->can('Restore:StatementsOfApplicability');
    }

    public function forceDelete(AuthUser $authUser, StatementsOfApplicability $statementsOfApplicability): bool
    {
        return $authUser->can('ForceDelete:StatementsOfApplicability');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StatementsOfApplicability');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StatementsOfApplicability');
    }

    public function replicate(AuthUser $authUser, StatementsOfApplicability $statementsOfApplicability): bool
    {
        return $authUser->can('Replicate:StatementsOfApplicability');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StatementsOfApplicability');
    }
}
