<?php

namespace Modules\Member\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Member\Models\MemberProfile;

class MemberProfilePolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Member');
    }

    public function view(AuthUser $authUser, MemberProfile $memberProfile): bool
    {
        return $authUser->can('View:Member');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Member');
    }

    public function update(AuthUser $authUser, MemberProfile $memberProfile): bool
    {
        return $authUser->can('Update:Member');
    }

    public function delete(AuthUser $authUser, MemberProfile $memberProfile): bool
    {
        return $authUser->can('Delete:Member');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Member');
    }
}
