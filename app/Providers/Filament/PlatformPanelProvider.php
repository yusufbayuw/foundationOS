<?php

namespace App\Providers\Filament;

use App\Filament\Platform\Pages\PlatformDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Core\Http\Middleware\SetUserLocale;

class PlatformPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('platform')
            ->path('platform')
            ->login()
            ->passwordReset()
            ->colors(['primary' => Color::Violet])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->topNavigation(false)
            ->discoverResources(
                in: app_path('Filament/Platform/Resources'),
                for: 'App\Filament\Platform\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/Platform/Pages'),
                for: 'App\Filament\Platform\Pages',
            )
            ->pages([PlatformDashboard::class])
            ->discoverWidgets(
                in: app_path('Filament/Platform/Widgets'),
                for: 'App\Filament\Platform\Widgets',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ShareErrorsFromSession::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                SetUserLocale::class,
                'role:platform_owner',
            ]);
    }
}
