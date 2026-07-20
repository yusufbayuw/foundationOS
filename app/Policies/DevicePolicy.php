<?php

namespace App\Policies;

use App\Models\Device;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DevicePolicy
{
    use HandlesAuthorization;

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function delete(AuthUser $authUser, ?Device $device = null): bool
    {
        return true;
    }
}
