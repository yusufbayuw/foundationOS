<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryStockTakeItem;

class LibraryStockTakeItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryStockTakeItem');
    }

    public function view(AuthUser $authUser, LibraryStockTakeItem $libraryStockTakeItem): bool
    {
        return $authUser->can('View:LibraryStockTakeItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryStockTakeItem');
    }

    public function update(AuthUser $authUser, LibraryStockTakeItem $libraryStockTakeItem): bool
    {
        return $authUser->can('Update:LibraryStockTakeItem');
    }

    public function delete(AuthUser $authUser, LibraryStockTakeItem $libraryStockTakeItem): bool
    {
        return $authUser->can('Delete:LibraryStockTakeItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryStockTakeItem');
    }

    public function restore(AuthUser $authUser, LibraryStockTakeItem $libraryStockTakeItem): bool
    {
        return $authUser->can('Restore:LibraryStockTakeItem');
    }

    public function forceDelete(AuthUser $authUser, LibraryStockTakeItem $libraryStockTakeItem): bool
    {
        return $authUser->can('ForceDelete:LibraryStockTakeItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryStockTakeItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryStockTakeItem');
    }

    public function replicate(AuthUser $authUser, LibraryStockTakeItem $libraryStockTakeItem): bool
    {
        return $authUser->can('Replicate:LibraryStockTakeItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryStockTakeItem');
    }
}
