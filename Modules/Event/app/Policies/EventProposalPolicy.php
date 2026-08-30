<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Event\Models\EventProposal;

class EventProposalPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventProposal');
    }

    public function view(AuthUser $authUser, EventProposal $eventProposal): bool
    {
        return $authUser->can('View:EventProposal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventProposal');
    }

    public function update(AuthUser $authUser, EventProposal $eventProposal): bool
    {
        return $authUser->can('Update:EventProposal');
    }

    public function delete(AuthUser $authUser, EventProposal $eventProposal): bool
    {
        return $authUser->can('Delete:EventProposal');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventProposal');
    }

    public function restore(AuthUser $authUser, EventProposal $eventProposal): bool
    {
        return $authUser->can('Restore:EventProposal');
    }

    public function forceDelete(AuthUser $authUser, EventProposal $eventProposal): bool
    {
        return $authUser->can('ForceDelete:EventProposal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventProposal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventProposal');
    }

    public function replicate(AuthUser $authUser, EventProposal $eventProposal): bool
    {
        return $authUser->can('Replicate:EventProposal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventProposal');
    }
}
