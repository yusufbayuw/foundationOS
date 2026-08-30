<?php

declare(strict_types=1);

namespace Modules\Donation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Donation\Models\CampaignUpdate;

class CampaignUpdatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CampaignUpdate');
    }

    public function view(AuthUser $authUser, CampaignUpdate $campaignUpdate): bool
    {
        return $authUser->can('View:CampaignUpdate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CampaignUpdate');
    }

    public function update(AuthUser $authUser, CampaignUpdate $campaignUpdate): bool
    {
        return $authUser->can('Update:CampaignUpdate');
    }

    public function delete(AuthUser $authUser, CampaignUpdate $campaignUpdate): bool
    {
        return $authUser->can('Delete:CampaignUpdate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CampaignUpdate');
    }

    public function restore(AuthUser $authUser, CampaignUpdate $campaignUpdate): bool
    {
        return $authUser->can('Restore:CampaignUpdate');
    }

    public function forceDelete(AuthUser $authUser, CampaignUpdate $campaignUpdate): bool
    {
        return $authUser->can('ForceDelete:CampaignUpdate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CampaignUpdate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CampaignUpdate');
    }

    public function replicate(AuthUser $authUser, CampaignUpdate $campaignUpdate): bool
    {
        return $authUser->can('Replicate:CampaignUpdate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CampaignUpdate');
    }
}
