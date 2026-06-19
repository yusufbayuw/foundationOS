<?php

declare(strict_types=1);

namespace Modules\Monitoring\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Monitoring\Models\FileUpload;

class FileUploadPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FileUpload');
    }

    public function view(AuthUser $authUser, FileUpload $fileUpload): bool
    {
        return $authUser->can('View:FileUpload');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FileUpload');
    }

    public function update(AuthUser $authUser, FileUpload $fileUpload): bool
    {
        return $authUser->can('Update:FileUpload');
    }

    public function delete(AuthUser $authUser, FileUpload $fileUpload): bool
    {
        return $authUser->can('Delete:FileUpload');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FileUpload');
    }

    public function restore(AuthUser $authUser, FileUpload $fileUpload): bool
    {
        return $authUser->can('Restore:FileUpload');
    }

    public function forceDelete(AuthUser $authUser, FileUpload $fileUpload): bool
    {
        return $authUser->can('ForceDelete:FileUpload');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FileUpload');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FileUpload');
    }

    public function replicate(AuthUser $authUser, FileUpload $fileUpload): bool
    {
        return $authUser->can('Replicate:FileUpload');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FileUpload');
    }
}
