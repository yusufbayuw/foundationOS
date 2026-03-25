<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\StudentInvoiceItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentInvoiceItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentInvoiceItem');
    }

    public function view(AuthUser $authUser, StudentInvoiceItem $studentInvoiceItem): bool
    {
        return $authUser->can('View:StudentInvoiceItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentInvoiceItem');
    }

    public function update(AuthUser $authUser, StudentInvoiceItem $studentInvoiceItem): bool
    {
        return $authUser->can('Update:StudentInvoiceItem');
    }

    public function delete(AuthUser $authUser, StudentInvoiceItem $studentInvoiceItem): bool
    {
        return $authUser->can('Delete:StudentInvoiceItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentInvoiceItem');
    }

    public function restore(AuthUser $authUser, StudentInvoiceItem $studentInvoiceItem): bool
    {
        return $authUser->can('Restore:StudentInvoiceItem');
    }

    public function forceDelete(AuthUser $authUser, StudentInvoiceItem $studentInvoiceItem): bool
    {
        return $authUser->can('ForceDelete:StudentInvoiceItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentInvoiceItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentInvoiceItem');
    }

    public function replicate(AuthUser $authUser, StudentInvoiceItem $studentInvoiceItem): bool
    {
        return $authUser->can('Replicate:StudentInvoiceItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentInvoiceItem');
    }

}