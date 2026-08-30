<?php

namespace Modules\Alumni\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\JobApplication;

class JobApplicationPolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JobApplication');
    }

    public function view(AuthUser $authUser, JobApplication $jobApplication): bool
    {
        return $authUser->can('View:JobApplication');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JobApplication');
    }

    public function update(AuthUser $authUser, JobApplication $jobApplication): bool
    {
        return $authUser->can('Update:JobApplication');
    }

    public function delete(AuthUser $authUser, JobApplication $jobApplication): bool
    {
        return $authUser->can('Delete:JobApplication');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JobApplication');
    }

    public function restore(AuthUser $authUser, JobApplication $jobApplication): bool
    {
        return $authUser->can('Restore:JobApplication');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JobApplication');
    }

    public function forceDelete(AuthUser $authUser, JobApplication $jobApplication): bool
    {
        return $authUser->can('ForceDelete:JobApplication');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JobApplication');
    }

    public function replicate(AuthUser $authUser, JobApplication $jobApplication): bool
    {
        return $authUser->can('Replicate:JobApplication');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JobApplication');
    }
}
