<?php

declare(strict_types=1);

namespace Modules\Risk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Risk\Models\KeyRiskIndicator;

class KeyRiskIndicatorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KeyRiskIndicator');
    }

    public function view(AuthUser $authUser, KeyRiskIndicator $keyRiskIndicator): bool
    {
        return $authUser->can('View:KeyRiskIndicator');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KeyRiskIndicator');
    }

    public function update(AuthUser $authUser, KeyRiskIndicator $keyRiskIndicator): bool
    {
        return $authUser->can('Update:KeyRiskIndicator');
    }

    public function delete(AuthUser $authUser, KeyRiskIndicator $keyRiskIndicator): bool
    {
        return $authUser->can('Delete:KeyRiskIndicator');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KeyRiskIndicator');
    }

    public function restore(AuthUser $authUser, KeyRiskIndicator $keyRiskIndicator): bool
    {
        return $authUser->can('Restore:KeyRiskIndicator');
    }

    public function forceDelete(AuthUser $authUser, KeyRiskIndicator $keyRiskIndicator): bool
    {
        return $authUser->can('ForceDelete:KeyRiskIndicator');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KeyRiskIndicator');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KeyRiskIndicator');
    }

    public function replicate(AuthUser $authUser, KeyRiskIndicator $keyRiskIndicator): bool
    {
        return $authUser->can('Replicate:KeyRiskIndicator');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KeyRiskIndicator');
    }
}
