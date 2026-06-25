<?php

namespace Modules\Library\Services\Opac;

use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class CirculationAuthorizationService
{
    public function authorize(?User $user, Tenant $tenant): void
    {
        abort_unless($user !== null && ($user->isGlobalSuperAdmin() || $user->canAccessTenant($tenant)), 403);
        abort_unless($user->isGlobalSuperAdmin() || $user->can('Create:Loan') || $user->can('Update:Loan') || $user->can('ViewAny:Loan'), 403);
    }
}
