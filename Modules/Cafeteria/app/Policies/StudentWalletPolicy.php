<?php

declare(strict_types=1);

namespace Modules\Cafeteria\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cafeteria\Models\StudentWallet;

class StudentWalletPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentWallet');
    }

    public function view(AuthUser $authUser, StudentWallet $studentWallet): bool
    {
        return $authUser->can('View:StudentWallet');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentWallet');
    }

    public function update(AuthUser $authUser, StudentWallet $studentWallet): bool
    {
        return $authUser->can('Update:StudentWallet');
    }

    public function delete(AuthUser $authUser, StudentWallet $studentWallet): bool
    {
        return $authUser->can('Delete:StudentWallet');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentWallet');
    }

    public function restore(AuthUser $authUser, StudentWallet $studentWallet): bool
    {
        return $authUser->can('Restore:StudentWallet');
    }

    public function forceDelete(AuthUser $authUser, StudentWallet $studentWallet): bool
    {
        return $authUser->can('ForceDelete:StudentWallet');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentWallet');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentWallet');
    }

    public function replicate(AuthUser $authUser, StudentWallet $studentWallet): bool
    {
        return $authUser->can('Replicate:StudentWallet');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentWallet');
    }
}
