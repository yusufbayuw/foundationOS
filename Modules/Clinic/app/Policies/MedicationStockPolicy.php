<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Clinic\Models\MedicationStock;

class MedicationStockPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MedicationStock');
    }

    public function view(AuthUser $authUser, MedicationStock $medicationStock): bool
    {
        return $authUser->can('View:MedicationStock');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MedicationStock');
    }

    public function update(AuthUser $authUser, MedicationStock $medicationStock): bool
    {
        return $authUser->can('Update:MedicationStock');
    }

    public function delete(AuthUser $authUser, MedicationStock $medicationStock): bool
    {
        return $authUser->can('Delete:MedicationStock');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MedicationStock');
    }

    public function restore(AuthUser $authUser, MedicationStock $medicationStock): bool
    {
        return $authUser->can('Restore:MedicationStock');
    }

    public function forceDelete(AuthUser $authUser, MedicationStock $medicationStock): bool
    {
        return $authUser->can('ForceDelete:MedicationStock');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MedicationStock');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MedicationStock');
    }

    public function replicate(AuthUser $authUser, MedicationStock $medicationStock): bool
    {
        return $authUser->can('Replicate:MedicationStock');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MedicationStock');
    }
}
