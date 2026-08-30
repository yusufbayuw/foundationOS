<?php

declare(strict_types=1);

namespace Modules\Consulting\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Consulting\Models\ConsultingClient;

class ConsultingClientPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConsultingClient');
    }

    public function view(AuthUser $authUser, ConsultingClient $consultingClient): bool
    {
        return $authUser->can('View:ConsultingClient');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConsultingClient');
    }

    public function update(AuthUser $authUser, ConsultingClient $consultingClient): bool
    {
        return $authUser->can('Update:ConsultingClient');
    }

    public function delete(AuthUser $authUser, ConsultingClient $consultingClient): bool
    {
        return $authUser->can('Delete:ConsultingClient');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ConsultingClient');
    }

    public function restore(AuthUser $authUser, ConsultingClient $consultingClient): bool
    {
        return $authUser->can('Restore:ConsultingClient');
    }

    public function forceDelete(AuthUser $authUser, ConsultingClient $consultingClient): bool
    {
        return $authUser->can('ForceDelete:ConsultingClient');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConsultingClient');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConsultingClient');
    }

    public function replicate(AuthUser $authUser, ConsultingClient $consultingClient): bool
    {
        return $authUser->can('Replicate:ConsultingClient');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConsultingClient');
    }
}
