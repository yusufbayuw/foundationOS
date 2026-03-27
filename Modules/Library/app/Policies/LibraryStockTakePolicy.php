<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryStockTake;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryStockTakePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryStockTake');
    }

    public function view(AuthUser $authUser, LibraryStockTake $libraryStockTake): bool
    {
        return $authUser->can('View:LibraryStockTake');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryStockTake');
    }

    public function update(AuthUser $authUser, LibraryStockTake $libraryStockTake): bool
    {
        return $authUser->can('Update:LibraryStockTake');
    }

    public function delete(AuthUser $authUser, LibraryStockTake $libraryStockTake): bool
    {
        return $authUser->can('Delete:LibraryStockTake');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryStockTake');
    }

    public function restore(AuthUser $authUser, LibraryStockTake $libraryStockTake): bool
    {
        return $authUser->can('Restore:LibraryStockTake');
    }

    public function forceDelete(AuthUser $authUser, LibraryStockTake $libraryStockTake): bool
    {
        return $authUser->can('ForceDelete:LibraryStockTake');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryStockTake');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryStockTake');
    }

    public function replicate(AuthUser $authUser, LibraryStockTake $libraryStockTake): bool
    {
        return $authUser->can('Replicate:LibraryStockTake');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryStockTake');
    }

}