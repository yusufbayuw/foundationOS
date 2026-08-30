<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\CompanyPartner;

class CompanyPartnerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CompanyPartner');
    }

    public function view(AuthUser $authUser, CompanyPartner $companyPartner): bool
    {
        return $authUser->can('View:CompanyPartner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CompanyPartner');
    }

    public function update(AuthUser $authUser, CompanyPartner $companyPartner): bool
    {
        return $authUser->can('Update:CompanyPartner');
    }

    public function delete(AuthUser $authUser, CompanyPartner $companyPartner): bool
    {
        return $authUser->can('Delete:CompanyPartner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CompanyPartner');
    }

    public function restore(AuthUser $authUser, CompanyPartner $companyPartner): bool
    {
        return $authUser->can('Restore:CompanyPartner');
    }

    public function forceDelete(AuthUser $authUser, CompanyPartner $companyPartner): bool
    {
        return $authUser->can('ForceDelete:CompanyPartner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CompanyPartner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CompanyPartner');
    }

    public function replicate(AuthUser $authUser, CompanyPartner $companyPartner): bool
    {
        return $authUser->can('Replicate:CompanyPartner');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CompanyPartner');
    }
}
