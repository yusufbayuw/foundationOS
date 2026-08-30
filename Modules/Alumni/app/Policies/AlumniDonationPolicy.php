<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\AlumniDonation;

class AlumniDonationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AlumniDonation');
    }

    public function view(AuthUser $authUser, AlumniDonation $alumniDonation): bool
    {
        return $authUser->can('View:AlumniDonation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AlumniDonation');
    }

    public function update(AuthUser $authUser, AlumniDonation $alumniDonation): bool
    {
        return $authUser->can('Update:AlumniDonation');
    }

    public function delete(AuthUser $authUser, AlumniDonation $alumniDonation): bool
    {
        return $authUser->can('Delete:AlumniDonation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AlumniDonation');
    }

    public function restore(AuthUser $authUser, AlumniDonation $alumniDonation): bool
    {
        return $authUser->can('Restore:AlumniDonation');
    }

    public function forceDelete(AuthUser $authUser, AlumniDonation $alumniDonation): bool
    {
        return $authUser->can('ForceDelete:AlumniDonation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AlumniDonation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AlumniDonation');
    }

    public function replicate(AuthUser $authUser, AlumniDonation $alumniDonation): bool
    {
        return $authUser->can('Replicate:AlumniDonation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AlumniDonation');
    }
}
