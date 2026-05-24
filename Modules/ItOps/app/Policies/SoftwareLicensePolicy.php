<?php

declare(strict_types=1);

namespace Modules\ItOps\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ItOps\Models\SoftwareLicense;

class SoftwareLicensePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SoftwareLicense');
    }

    public function view(AuthUser $authUser, SoftwareLicense $softwareLicense): bool
    {
        return $authUser->can('View:SoftwareLicense');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SoftwareLicense');
    }

    public function update(AuthUser $authUser, SoftwareLicense $softwareLicense): bool
    {
        return $authUser->can('Update:SoftwareLicense');
    }

    public function delete(AuthUser $authUser, SoftwareLicense $softwareLicense): bool
    {
        return $authUser->can('Delete:SoftwareLicense');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SoftwareLicense');
    }

    public function restore(AuthUser $authUser, SoftwareLicense $softwareLicense): bool
    {
        return $authUser->can('Restore:SoftwareLicense');
    }

    public function forceDelete(AuthUser $authUser, SoftwareLicense $softwareLicense): bool
    {
        return $authUser->can('ForceDelete:SoftwareLicense');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SoftwareLicense');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SoftwareLicense');
    }

    public function replicate(AuthUser $authUser, SoftwareLicense $softwareLicense): bool
    {
        return $authUser->can('Replicate:SoftwareLicense');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SoftwareLicense');
    }
}
