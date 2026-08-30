<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\CustomerInvoiceItem;

class CustomerInvoiceItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CustomerInvoiceItem');
    }

    public function view(AuthUser $authUser, CustomerInvoiceItem $customerInvoiceItem): bool
    {
        return $authUser->can('View:CustomerInvoiceItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CustomerInvoiceItem');
    }

    public function update(AuthUser $authUser, CustomerInvoiceItem $customerInvoiceItem): bool
    {
        return $authUser->can('Update:CustomerInvoiceItem');
    }

    public function delete(AuthUser $authUser, CustomerInvoiceItem $customerInvoiceItem): bool
    {
        return $authUser->can('Delete:CustomerInvoiceItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CustomerInvoiceItem');
    }

    public function restore(AuthUser $authUser, CustomerInvoiceItem $customerInvoiceItem): bool
    {
        return $authUser->can('Restore:CustomerInvoiceItem');
    }

    public function forceDelete(AuthUser $authUser, CustomerInvoiceItem $customerInvoiceItem): bool
    {
        return $authUser->can('ForceDelete:CustomerInvoiceItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CustomerInvoiceItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CustomerInvoiceItem');
    }

    public function replicate(AuthUser $authUser, CustomerInvoiceItem $customerInvoiceItem): bool
    {
        return $authUser->can('Replicate:CustomerInvoiceItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CustomerInvoiceItem');
    }
}
