<?php

declare(strict_types=1);

namespace Modules\Consulting\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Consulting\Models\ConsultingEngagement;

class ConsultingEngagementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConsultingEngagement');
    }

    public function view(AuthUser $authUser, ConsultingEngagement $consultingEngagement): bool
    {
        return $authUser->can('View:ConsultingEngagement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConsultingEngagement');
    }

    public function update(AuthUser $authUser, ConsultingEngagement $consultingEngagement): bool
    {
        return $authUser->can('Update:ConsultingEngagement');
    }

    public function delete(AuthUser $authUser, ConsultingEngagement $consultingEngagement): bool
    {
        return $authUser->can('Delete:ConsultingEngagement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ConsultingEngagement');
    }

    public function restore(AuthUser $authUser, ConsultingEngagement $consultingEngagement): bool
    {
        return $authUser->can('Restore:ConsultingEngagement');
    }

    public function forceDelete(AuthUser $authUser, ConsultingEngagement $consultingEngagement): bool
    {
        return $authUser->can('ForceDelete:ConsultingEngagement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConsultingEngagement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConsultingEngagement');
    }

    public function replicate(AuthUser $authUser, ConsultingEngagement $consultingEngagement): bool
    {
        return $authUser->can('Replicate:ConsultingEngagement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConsultingEngagement');
    }
}
