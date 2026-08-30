<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MoodleSyncOutbox;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MoodleSyncOutboxPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MoodleSyncOutbox');
    }

    public function view(AuthUser $authUser, MoodleSyncOutbox $moodleSyncOutbox): bool
    {
        return $authUser->can('View:MoodleSyncOutbox');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MoodleSyncOutbox');
    }

    public function update(AuthUser $authUser, MoodleSyncOutbox $moodleSyncOutbox): bool
    {
        return $authUser->can('Update:MoodleSyncOutbox');
    }

    public function delete(AuthUser $authUser, MoodleSyncOutbox $moodleSyncOutbox): bool
    {
        return $authUser->can('Delete:MoodleSyncOutbox');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MoodleSyncOutbox');
    }

    public function restore(AuthUser $authUser, MoodleSyncOutbox $moodleSyncOutbox): bool
    {
        return $authUser->can('Restore:MoodleSyncOutbox');
    }

    public function forceDelete(AuthUser $authUser, MoodleSyncOutbox $moodleSyncOutbox): bool
    {
        return $authUser->can('ForceDelete:MoodleSyncOutbox');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MoodleSyncOutbox');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MoodleSyncOutbox');
    }

    public function replicate(AuthUser $authUser, MoodleSyncOutbox $moodleSyncOutbox): bool
    {
        return $authUser->can('Replicate:MoodleSyncOutbox');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MoodleSyncOutbox');
    }
}
