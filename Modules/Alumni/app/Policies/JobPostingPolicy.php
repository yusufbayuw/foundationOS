<?php

namespace Modules\Alumni\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\JobPosting;

class JobPostingPolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JobPosting');
    }

    public function view(AuthUser $authUser, JobPosting $jobPosting): bool
    {
        return $authUser->can('View:JobPosting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JobPosting');
    }

    public function update(AuthUser $authUser, JobPosting $jobPosting): bool
    {
        return $authUser->can('Update:JobPosting');
    }

    public function delete(AuthUser $authUser, JobPosting $jobPosting): bool
    {
        return $authUser->can('Delete:JobPosting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JobPosting');
    }

    public function restore(AuthUser $authUser, JobPosting $jobPosting): bool
    {
        return $authUser->can('Restore:JobPosting');
    }

    public function forceDelete(AuthUser $authUser, JobPosting $jobPosting): bool
    {
        return $authUser->can('ForceDelete:JobPosting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JobPosting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JobPosting');
    }

    public function replicate(AuthUser $authUser, JobPosting $jobPosting): bool
    {
        return $authUser->can('Replicate:JobPosting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JobPosting');
    }
}
