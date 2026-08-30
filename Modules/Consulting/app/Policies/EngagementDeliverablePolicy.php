<?php

declare(strict_types=1);

namespace Modules\Consulting\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Consulting\Models\EngagementDeliverable;

class EngagementDeliverablePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EngagementDeliverable');
    }

    public function view(AuthUser $authUser, EngagementDeliverable $engagementDeliverable): bool
    {
        return $authUser->can('View:EngagementDeliverable');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EngagementDeliverable');
    }

    public function update(AuthUser $authUser, EngagementDeliverable $engagementDeliverable): bool
    {
        return $authUser->can('Update:EngagementDeliverable');
    }

    public function delete(AuthUser $authUser, EngagementDeliverable $engagementDeliverable): bool
    {
        return $authUser->can('Delete:EngagementDeliverable');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EngagementDeliverable');
    }

    public function restore(AuthUser $authUser, EngagementDeliverable $engagementDeliverable): bool
    {
        return $authUser->can('Restore:EngagementDeliverable');
    }

    public function forceDelete(AuthUser $authUser, EngagementDeliverable $engagementDeliverable): bool
    {
        return $authUser->can('ForceDelete:EngagementDeliverable');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EngagementDeliverable');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EngagementDeliverable');
    }

    public function replicate(AuthUser $authUser, EngagementDeliverable $engagementDeliverable): bool
    {
        return $authUser->can('Replicate:EngagementDeliverable');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EngagementDeliverable');
    }
}
