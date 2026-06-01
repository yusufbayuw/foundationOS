<?php

declare(strict_types=1);

namespace Modules\Training\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Policies\Concerns\AuthorizesPrint;
use Modules\Training\Models\TrainingCertificate;

class TrainingCertificatePolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingCertificate');
    }

    public function view(AuthUser $authUser, TrainingCertificate $trainingCertificate): bool
    {
        return $authUser->can('View:TrainingCertificate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingCertificate');
    }

    public function update(AuthUser $authUser, TrainingCertificate $trainingCertificate): bool
    {
        return $authUser->can('Update:TrainingCertificate');
    }

    public function delete(AuthUser $authUser, TrainingCertificate $trainingCertificate): bool
    {
        return $authUser->can('Delete:TrainingCertificate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TrainingCertificate');
    }

    public function restore(AuthUser $authUser, TrainingCertificate $trainingCertificate): bool
    {
        return $authUser->can('Restore:TrainingCertificate');
    }

    public function forceDelete(AuthUser $authUser, TrainingCertificate $trainingCertificate): bool
    {
        return $authUser->can('ForceDelete:TrainingCertificate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingCertificate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingCertificate');
    }

    public function replicate(AuthUser $authUser, TrainingCertificate $trainingCertificate): bool
    {
        return $authUser->can('Replicate:TrainingCertificate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingCertificate');
    }
}
