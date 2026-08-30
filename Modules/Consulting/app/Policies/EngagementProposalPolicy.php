<?php

declare(strict_types=1);

namespace Modules\Consulting\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Consulting\Models\EngagementProposal;

class EngagementProposalPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EngagementProposal');
    }

    public function view(AuthUser $authUser, EngagementProposal $engagementProposal): bool
    {
        return $authUser->can('View:EngagementProposal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EngagementProposal');
    }

    public function update(AuthUser $authUser, EngagementProposal $engagementProposal): bool
    {
        return $authUser->can('Update:EngagementProposal');
    }

    public function delete(AuthUser $authUser, EngagementProposal $engagementProposal): bool
    {
        return $authUser->can('Delete:EngagementProposal');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EngagementProposal');
    }

    public function restore(AuthUser $authUser, EngagementProposal $engagementProposal): bool
    {
        return $authUser->can('Restore:EngagementProposal');
    }

    public function forceDelete(AuthUser $authUser, EngagementProposal $engagementProposal): bool
    {
        return $authUser->can('ForceDelete:EngagementProposal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EngagementProposal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EngagementProposal');
    }

    public function replicate(AuthUser $authUser, EngagementProposal $engagementProposal): bool
    {
        return $authUser->can('Replicate:EngagementProposal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EngagementProposal');
    }
}
