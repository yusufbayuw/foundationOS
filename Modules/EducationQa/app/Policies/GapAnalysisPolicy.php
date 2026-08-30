<?php

declare(strict_types=1);

namespace Modules\EducationQa\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EducationQa\Models\GapAnalysis;

class GapAnalysisPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GapAnalysis');
    }

    public function view(AuthUser $authUser, GapAnalysis $gapAnalysis): bool
    {
        return $authUser->can('View:GapAnalysis');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GapAnalysis');
    }

    public function update(AuthUser $authUser, GapAnalysis $gapAnalysis): bool
    {
        return $authUser->can('Update:GapAnalysis');
    }

    public function delete(AuthUser $authUser, GapAnalysis $gapAnalysis): bool
    {
        return $authUser->can('Delete:GapAnalysis');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GapAnalysis');
    }

    public function restore(AuthUser $authUser, GapAnalysis $gapAnalysis): bool
    {
        return $authUser->can('Restore:GapAnalysis');
    }

    public function forceDelete(AuthUser $authUser, GapAnalysis $gapAnalysis): bool
    {
        return $authUser->can('ForceDelete:GapAnalysis');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GapAnalysis');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GapAnalysis');
    }

    public function replicate(AuthUser $authUser, GapAnalysis $gapAnalysis): bool
    {
        return $authUser->can('Replicate:GapAnalysis');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GapAnalysis');
    }
}
