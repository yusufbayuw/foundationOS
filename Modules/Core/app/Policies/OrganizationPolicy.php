<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\Organization;
use Modules\Core\Policies\Concerns\AuthorizesTenantScopedRecord;

class OrganizationPolicy
{
    use AuthorizesTenantScopedRecord;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Organization');
    }

    public function view(AuthUser $authUser, Organization $organization): bool
    {
        return $authUser->can('View:Organization')
            && $this->belongsToActiveTenant($authUser, $organization);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Organization');
    }

    public function update(AuthUser $authUser, Organization $organization): bool
    {
        return $authUser->can('Update:Organization')
            && $this->belongsToActiveTenant($authUser, $organization);
    }

    public function delete(AuthUser $authUser, Organization $organization): bool
    {
        return $authUser->can('Delete:Organization')
            && $this->belongsToActiveTenant($authUser, $organization);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Organization');
    }

    public function restore(AuthUser $authUser, Organization $organization): bool
    {
        return $authUser->can('Restore:Organization')
            && $this->belongsToActiveTenant($authUser, $organization);
    }

    public function forceDelete(AuthUser $authUser, Organization $organization): bool
    {
        return $authUser->can('ForceDelete:Organization')
            && $this->belongsToActiveTenant($authUser, $organization);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Organization');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Organization');
    }

    public function replicate(AuthUser $authUser, Organization $organization): bool
    {
        return $authUser->can('Replicate:Organization')
            && $this->belongsToActiveTenant($authUser, $organization);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Organization');
    }
}
