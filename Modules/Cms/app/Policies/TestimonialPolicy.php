<?php

declare(strict_types=1);

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cms\Models\Testimonial;

class TestimonialPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Testimonial');
    }

    public function view(AuthUser $authUser, Testimonial $testimonial): bool
    {
        return $authUser->can('View:Testimonial');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Testimonial');
    }

    public function update(AuthUser $authUser, Testimonial $testimonial): bool
    {
        return $authUser->can('Update:Testimonial');
    }

    public function delete(AuthUser $authUser, Testimonial $testimonial): bool
    {
        return $authUser->can('Delete:Testimonial');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Testimonial');
    }

    public function restore(AuthUser $authUser, Testimonial $testimonial): bool
    {
        return $authUser->can('Restore:Testimonial');
    }

    public function forceDelete(AuthUser $authUser, Testimonial $testimonial): bool
    {
        return $authUser->can('ForceDelete:Testimonial');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Testimonial');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Testimonial');
    }

    public function replicate(AuthUser $authUser, Testimonial $testimonial): bool
    {
        return $authUser->can('Replicate:Testimonial');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Testimonial');
    }
}
