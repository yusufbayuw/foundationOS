<?php

declare(strict_types=1);

namespace Modules\Donation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Donation\Models\RecurringDonation;

class RecurringDonationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RecurringDonation');
    }

    public function view(AuthUser $authUser, RecurringDonation $recurringDonation): bool
    {
        return $authUser->can('View:RecurringDonation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RecurringDonation');
    }

    public function update(AuthUser $authUser, RecurringDonation $recurringDonation): bool
    {
        return $authUser->can('Update:RecurringDonation');
    }

    public function delete(AuthUser $authUser, RecurringDonation $recurringDonation): bool
    {
        return $authUser->can('Delete:RecurringDonation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RecurringDonation');
    }

    public function restore(AuthUser $authUser, RecurringDonation $recurringDonation): bool
    {
        return $authUser->can('Restore:RecurringDonation');
    }

    public function forceDelete(AuthUser $authUser, RecurringDonation $recurringDonation): bool
    {
        return $authUser->can('ForceDelete:RecurringDonation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RecurringDonation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RecurringDonation');
    }

    public function replicate(AuthUser $authUser, RecurringDonation $recurringDonation): bool
    {
        return $authUser->can('Replicate:RecurringDonation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RecurringDonation');
    }
}
