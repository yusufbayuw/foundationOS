<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Policies\Concerns\AuthorizesPrint;
use Modules\Library\Models\Fine;

class FinePolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Fine');
    }

    public function view(AuthUser $authUser, Fine $fine): bool
    {
        return $authUser->can('View:Fine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Fine');
    }

    public function update(AuthUser $authUser, Fine $fine): bool
    {
        return $authUser->can('Update:Fine');
    }

    public function delete(AuthUser $authUser, Fine $fine): bool
    {
        return $authUser->can('Delete:Fine');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Fine');
    }

    public function restore(AuthUser $authUser, Fine $fine): bool
    {
        return $authUser->can('Restore:Fine');
    }

    public function forceDelete(AuthUser $authUser, Fine $fine): bool
    {
        return $authUser->can('ForceDelete:Fine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Fine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Fine');
    }

    public function replicate(AuthUser $authUser, Fine $fine): bool
    {
        return $authUser->can('Replicate:Fine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Fine');
    }
}
