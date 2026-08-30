<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\InternshipPosting;

class InternshipPostingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InternshipPosting');
    }

    public function view(AuthUser $authUser, InternshipPosting $internshipPosting): bool
    {
        return $authUser->can('View:InternshipPosting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InternshipPosting');
    }

    public function update(AuthUser $authUser, InternshipPosting $internshipPosting): bool
    {
        return $authUser->can('Update:InternshipPosting');
    }

    public function delete(AuthUser $authUser, InternshipPosting $internshipPosting): bool
    {
        return $authUser->can('Delete:InternshipPosting');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InternshipPosting');
    }

    public function restore(AuthUser $authUser, InternshipPosting $internshipPosting): bool
    {
        return $authUser->can('Restore:InternshipPosting');
    }

    public function forceDelete(AuthUser $authUser, InternshipPosting $internshipPosting): bool
    {
        return $authUser->can('ForceDelete:InternshipPosting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InternshipPosting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InternshipPosting');
    }

    public function replicate(AuthUser $authUser, InternshipPosting $internshipPosting): bool
    {
        return $authUser->can('Replicate:InternshipPosting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InternshipPosting');
    }
}
