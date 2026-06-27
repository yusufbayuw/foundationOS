<?php

namespace App\Providers\Filament\Admin;

use App\Filament\Pages\EditProfile;
use App\Filament\Pages\Tenancy\RegisterTenant;
use App\Http\Middleware\EnsureTenantSubscriptionActive;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Core\Http\Middleware\SetUserLocale;
use Modules\Core\Models\Tenant;

class AdminPanelAuthConfigurator
{
    public function configure(Panel $panel): Panel
    {
        return $panel
            ->login()
            ->registration()
            ->emailVerification()
            ->passwordReset()
            ->tenantRegistration(RegisterTenant::class)
            ->profile(EditProfile::class)
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
                EmailAuthentication::make(),
            ])
            ->tenant(Tenant::class)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                SetUserLocale::class,
                EnsureTenantSubscriptionActive::class,
            ]);
    }
}
