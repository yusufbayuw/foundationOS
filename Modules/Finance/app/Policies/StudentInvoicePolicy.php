<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\StudentInvoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentInvoicePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentInvoice');
    }

    public function view(AuthUser $authUser, StudentInvoice $studentInvoice): bool
    {
        return $authUser->can('View:StudentInvoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentInvoice');
    }

    public function update(AuthUser $authUser, StudentInvoice $studentInvoice): bool
    {
        return $authUser->can('Update:StudentInvoice');
    }

    public function delete(AuthUser $authUser, StudentInvoice $studentInvoice): bool
    {
        return $authUser->can('Delete:StudentInvoice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentInvoice');
    }

    public function restore(AuthUser $authUser, StudentInvoice $studentInvoice): bool
    {
        return $authUser->can('Restore:StudentInvoice');
    }

    public function forceDelete(AuthUser $authUser, StudentInvoice $studentInvoice): bool
    {
        return $authUser->can('ForceDelete:StudentInvoice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentInvoice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentInvoice');
    }

    public function replicate(AuthUser $authUser, StudentInvoice $studentInvoice): bool
    {
        return $authUser->can('Replicate:StudentInvoice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentInvoice');
    }

}