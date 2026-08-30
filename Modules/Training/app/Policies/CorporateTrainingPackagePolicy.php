<?php

declare(strict_types=1);

namespace Modules\Training\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Training\Models\CorporateTrainingPackage;

class CorporateTrainingPackagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CorporateTrainingPackage');
    }

    public function view(AuthUser $authUser, CorporateTrainingPackage $corporateTrainingPackage): bool
    {
        return $authUser->can('View:CorporateTrainingPackage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CorporateTrainingPackage');
    }

    public function update(AuthUser $authUser, CorporateTrainingPackage $corporateTrainingPackage): bool
    {
        return $authUser->can('Update:CorporateTrainingPackage');
    }

    public function delete(AuthUser $authUser, CorporateTrainingPackage $corporateTrainingPackage): bool
    {
        return $authUser->can('Delete:CorporateTrainingPackage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CorporateTrainingPackage');
    }

    public function restore(AuthUser $authUser, CorporateTrainingPackage $corporateTrainingPackage): bool
    {
        return $authUser->can('Restore:CorporateTrainingPackage');
    }

    public function forceDelete(AuthUser $authUser, CorporateTrainingPackage $corporateTrainingPackage): bool
    {
        return $authUser->can('ForceDelete:CorporateTrainingPackage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CorporateTrainingPackage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CorporateTrainingPackage');
    }

    public function replicate(AuthUser $authUser, CorporateTrainingPackage $corporateTrainingPackage): bool
    {
        return $authUser->can('Replicate:CorporateTrainingPackage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CorporateTrainingPackage');
    }
}
