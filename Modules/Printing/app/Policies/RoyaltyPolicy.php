<?php

declare(strict_types=1);

namespace Modules\Printing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Printing\Models\Royalty;

class RoyaltyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Royalty');
    }

    public function view(AuthUser $authUser, Royalty $royalty): bool
    {
        return $authUser->can('View:Royalty');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Royalty');
    }

    public function update(AuthUser $authUser, Royalty $royalty): bool
    {
        return $authUser->can('Update:Royalty');
    }

    public function delete(AuthUser $authUser, Royalty $royalty): bool
    {
        return $authUser->can('Delete:Royalty');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Royalty');
    }

    public function restore(AuthUser $authUser, Royalty $royalty): bool
    {
        return $authUser->can('Restore:Royalty');
    }

    public function forceDelete(AuthUser $authUser, Royalty $royalty): bool
    {
        return $authUser->can('ForceDelete:Royalty');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Royalty');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Royalty');
    }

    public function replicate(AuthUser $authUser, Royalty $royalty): bool
    {
        return $authUser->can('Replicate:Royalty');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Royalty');
    }
}
