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
}
