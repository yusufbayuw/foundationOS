<?php

declare(strict_types=1);

namespace Modules\Donation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Donation\Models\Donor;

class DonorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Donor');
    }

    public function view(AuthUser $authUser, Donor $donor): bool
    {
        return $authUser->can('View:Donor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Donor');
    }

    public function update(AuthUser $authUser, Donor $donor): bool
    {
        return $authUser->can('Update:Donor');
    }

    public function delete(AuthUser $authUser, Donor $donor): bool
    {
        return $authUser->can('Delete:Donor');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:Donor');
    }
}
