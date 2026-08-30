<?php

declare(strict_types=1);

namespace Modules\Enrollment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Enrollment\Models\LeadSource;

class LeadSourcePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeadSource');
    }

    public function view(AuthUser $authUser, LeadSource $leadSource): bool
    {
        return $authUser->can('View:LeadSource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeadSource');
    }

    public function update(AuthUser $authUser, LeadSource $leadSource): bool
    {
        return $authUser->can('Update:LeadSource');
    }

    public function delete(AuthUser $authUser, LeadSource $leadSource): bool
    {
        return $authUser->can('Delete:LeadSource');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeadSource');
    }

    public function restore(AuthUser $authUser, LeadSource $leadSource): bool
    {
        return $authUser->can('Restore:LeadSource');
    }

    public function forceDelete(AuthUser $authUser, LeadSource $leadSource): bool
    {
        return $authUser->can('ForceDelete:LeadSource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeadSource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeadSource');
    }

    public function replicate(AuthUser $authUser, LeadSource $leadSource): bool
    {
        return $authUser->can('Replicate:LeadSource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeadSource');
    }
}
