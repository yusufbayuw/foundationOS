<?php

namespace Modules\Member\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Member\Models\MemberProof;

class MemberProofPolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Member');
    }

    public function view(AuthUser $authUser, MemberProof $memberProof): bool
    {
        return $authUser->can('View:Member');
    }

    public function create(AuthUser $authUser): bool
    {
        return false;
    }

    public function update(AuthUser $authUser, MemberProof $memberProof): bool
    {
        return $authUser->can('Update:Member');
    }

    public function delete(AuthUser $authUser, MemberProof $memberProof): bool
    {
        return false;
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return false;
    }
}
