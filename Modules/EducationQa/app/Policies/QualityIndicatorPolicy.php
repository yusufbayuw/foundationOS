<?php

declare(strict_types=1);

namespace Modules\EducationQa\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EducationQa\Models\QualityIndicator;

class QualityIndicatorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QualityIndicator');
    }

    public function view(AuthUser $authUser, QualityIndicator $qualityIndicator): bool
    {
        return $authUser->can('View:QualityIndicator');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QualityIndicator');
    }

    public function update(AuthUser $authUser, QualityIndicator $qualityIndicator): bool
    {
        return $authUser->can('Update:QualityIndicator');
    }

    public function delete(AuthUser $authUser, QualityIndicator $qualityIndicator): bool
    {
        return $authUser->can('Delete:QualityIndicator');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QualityIndicator');
    }

    public function restore(AuthUser $authUser, QualityIndicator $qualityIndicator): bool
    {
        return $authUser->can('Restore:QualityIndicator');
    }

    public function forceDelete(AuthUser $authUser, QualityIndicator $qualityIndicator): bool
    {
        return $authUser->can('ForceDelete:QualityIndicator');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QualityIndicator');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QualityIndicator');
    }

    public function replicate(AuthUser $authUser, QualityIndicator $qualityIndicator): bool
    {
        return $authUser->can('Replicate:QualityIndicator');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QualityIndicator');
    }
}
