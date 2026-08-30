<?php

declare(strict_types=1);

namespace Modules\Printing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Printing\Models\PrintProduction;

class PrintProductionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrintProduction');
    }

    public function view(AuthUser $authUser, PrintProduction $printProduction): bool
    {
        return $authUser->can('View:PrintProduction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrintProduction');
    }

    public function update(AuthUser $authUser, PrintProduction $printProduction): bool
    {
        return $authUser->can('Update:PrintProduction');
    }

    public function delete(AuthUser $authUser, PrintProduction $printProduction): bool
    {
        return $authUser->can('Delete:PrintProduction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrintProduction');
    }

    public function restore(AuthUser $authUser, PrintProduction $printProduction): bool
    {
        return $authUser->can('Restore:PrintProduction');
    }

    public function forceDelete(AuthUser $authUser, PrintProduction $printProduction): bool
    {
        return $authUser->can('ForceDelete:PrintProduction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrintProduction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrintProduction');
    }

    public function replicate(AuthUser $authUser, PrintProduction $printProduction): bool
    {
        return $authUser->can('Replicate:PrintProduction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrintProduction');
    }
}
