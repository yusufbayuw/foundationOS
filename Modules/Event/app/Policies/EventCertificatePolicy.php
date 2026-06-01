<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Policies\Concerns\AuthorizesPrint;
use Modules\Event\Models\EventCertificate;

class EventCertificatePolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventCertificate');
    }

    public function view(AuthUser $authUser, EventCertificate $eventCertificate): bool
    {
        return $authUser->can('View:EventCertificate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventCertificate');
    }

    public function update(AuthUser $authUser, EventCertificate $eventCertificate): bool
    {
        return $authUser->can('Update:EventCertificate');
    }

    public function delete(AuthUser $authUser, EventCertificate $eventCertificate): bool
    {
        return $authUser->can('Delete:EventCertificate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventCertificate');
    }

    public function restore(AuthUser $authUser, EventCertificate $eventCertificate): bool
    {
        return $authUser->can('Restore:EventCertificate');
    }

    public function forceDelete(AuthUser $authUser, EventCertificate $eventCertificate): bool
    {
        return $authUser->can('ForceDelete:EventCertificate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventCertificate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventCertificate');
    }

    public function replicate(AuthUser $authUser, EventCertificate $eventCertificate): bool
    {
        return $authUser->can('Replicate:EventCertificate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventCertificate');
    }
}
