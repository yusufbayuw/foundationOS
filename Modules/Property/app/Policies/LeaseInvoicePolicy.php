<?php

declare(strict_types=1);

namespace Modules\Property\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Policies\Concerns\AuthorizesPrint;
use Modules\Property\Models\LeaseInvoice;

class LeaseInvoicePolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeaseInvoice');
    }

    public function view(AuthUser $authUser, LeaseInvoice $leaseInvoice): bool
    {
        return $authUser->can('View:LeaseInvoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeaseInvoice');
    }

    public function update(AuthUser $authUser, LeaseInvoice $leaseInvoice): bool
    {
        return $authUser->can('Update:LeaseInvoice');
    }

    public function delete(AuthUser $authUser, LeaseInvoice $leaseInvoice): bool
    {
        return $authUser->can('Delete:LeaseInvoice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeaseInvoice');
    }

    public function restore(AuthUser $authUser, LeaseInvoice $leaseInvoice): bool
    {
        return $authUser->can('Restore:LeaseInvoice');
    }

    public function forceDelete(AuthUser $authUser, LeaseInvoice $leaseInvoice): bool
    {
        return $authUser->can('ForceDelete:LeaseInvoice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeaseInvoice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeaseInvoice');
    }

    public function replicate(AuthUser $authUser, LeaseInvoice $leaseInvoice): bool
    {
        return $authUser->can('Replicate:LeaseInvoice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeaseInvoice');
    }
}
