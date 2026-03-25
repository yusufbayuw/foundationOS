<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\RequestForQuotation;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestForQuotationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequestForQuotation');
    }

    public function view(AuthUser $authUser, RequestForQuotation $requestForQuotation): bool
    {
        return $authUser->can('View:RequestForQuotation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequestForQuotation');
    }

    public function update(AuthUser $authUser, RequestForQuotation $requestForQuotation): bool
    {
        return $authUser->can('Update:RequestForQuotation');
    }

    public function delete(AuthUser $authUser, RequestForQuotation $requestForQuotation): bool
    {
        return $authUser->can('Delete:RequestForQuotation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RequestForQuotation');
    }

    public function restore(AuthUser $authUser, RequestForQuotation $requestForQuotation): bool
    {
        return $authUser->can('Restore:RequestForQuotation');
    }

    public function forceDelete(AuthUser $authUser, RequestForQuotation $requestForQuotation): bool
    {
        return $authUser->can('ForceDelete:RequestForQuotation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RequestForQuotation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RequestForQuotation');
    }

    public function replicate(AuthUser $authUser, RequestForQuotation $requestForQuotation): bool
    {
        return $authUser->can('Replicate:RequestForQuotation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RequestForQuotation');
    }

}