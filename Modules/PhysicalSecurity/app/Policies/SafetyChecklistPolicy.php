<?php

declare(strict_types=1);

namespace Modules\PhysicalSecurity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PhysicalSecurity\Models\SafetyChecklist;

class SafetyChecklistPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SafetyChecklist');
    }

    public function view(AuthUser $authUser, SafetyChecklist $safetyChecklist): bool
    {
        return $authUser->can('View:SafetyChecklist');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SafetyChecklist');
    }

    public function update(AuthUser $authUser, SafetyChecklist $safetyChecklist): bool
    {
        return $authUser->can('Update:SafetyChecklist');
    }

    public function delete(AuthUser $authUser, SafetyChecklist $safetyChecklist): bool
    {
        return $authUser->can('Delete:SafetyChecklist');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SafetyChecklist');
    }

    public function restore(AuthUser $authUser, SafetyChecklist $safetyChecklist): bool
    {
        return $authUser->can('Restore:SafetyChecklist');
    }

    public function forceDelete(AuthUser $authUser, SafetyChecklist $safetyChecklist): bool
    {
        return $authUser->can('ForceDelete:SafetyChecklist');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SafetyChecklist');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SafetyChecklist');
    }

    public function replicate(AuthUser $authUser, SafetyChecklist $safetyChecklist): bool
    {
        return $authUser->can('Replicate:SafetyChecklist');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SafetyChecklist');
    }
}
