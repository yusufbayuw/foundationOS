<?php

declare(strict_types=1);

namespace Modules\Counseling\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Counseling\Models\WellbeingSurvey;

class WellbeingSurveyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WellbeingSurvey');
    }

    public function view(AuthUser $authUser, WellbeingSurvey $wellbeingSurvey): bool
    {
        return $authUser->can('View:WellbeingSurvey');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WellbeingSurvey');
    }

    public function update(AuthUser $authUser, WellbeingSurvey $wellbeingSurvey): bool
    {
        return $authUser->can('Update:WellbeingSurvey');
    }

    public function delete(AuthUser $authUser, WellbeingSurvey $wellbeingSurvey): bool
    {
        return $authUser->can('Delete:WellbeingSurvey');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WellbeingSurvey');
    }

    public function restore(AuthUser $authUser, WellbeingSurvey $wellbeingSurvey): bool
    {
        return $authUser->can('Restore:WellbeingSurvey');
    }

    public function forceDelete(AuthUser $authUser, WellbeingSurvey $wellbeingSurvey): bool
    {
        return $authUser->can('ForceDelete:WellbeingSurvey');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WellbeingSurvey');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WellbeingSurvey');
    }

    public function replicate(AuthUser $authUser, WellbeingSurvey $wellbeingSurvey): bool
    {
        return $authUser->can('Replicate:WellbeingSurvey');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WellbeingSurvey');
    }
}
