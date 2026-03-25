<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\JournalEntryLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalEntryLinePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JournalEntryLine');
    }

    public function view(AuthUser $authUser, JournalEntryLine $journalEntryLine): bool
    {
        return $authUser->can('View:JournalEntryLine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JournalEntryLine');
    }

    public function update(AuthUser $authUser, JournalEntryLine $journalEntryLine): bool
    {
        return $authUser->can('Update:JournalEntryLine');
    }

    public function delete(AuthUser $authUser, JournalEntryLine $journalEntryLine): bool
    {
        return $authUser->can('Delete:JournalEntryLine');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JournalEntryLine');
    }

    public function restore(AuthUser $authUser, JournalEntryLine $journalEntryLine): bool
    {
        return $authUser->can('Restore:JournalEntryLine');
    }

    public function forceDelete(AuthUser $authUser, JournalEntryLine $journalEntryLine): bool
    {
        return $authUser->can('ForceDelete:JournalEntryLine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JournalEntryLine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JournalEntryLine');
    }

    public function replicate(AuthUser $authUser, JournalEntryLine $journalEntryLine): bool
    {
        return $authUser->can('Replicate:JournalEntryLine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JournalEntryLine');
    }

}