<?php

namespace Modules\Core\Filament\Support\Guards;

class GlobalResourceGuard
{
    public static function isMutationRestricted(bool $isScopedToTenant): bool
    {
        return ! $isScopedToTenant;
    }

    public static function canCurrentUserMutate(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->isGlobalSuperAdmin());
    }
}
