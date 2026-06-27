<?php

namespace App\Providers\Filament;

use App\Filament\Pages\BillingPage;
use App\Filament\Pages\EditProfile;
use App\Filament\Pages\TabbedDashboard;
use App\Filament\Pages\Tenancy\RegisterTenant;
use App\Http\Middleware\BindTenantToContainer;
use App\Http\Middleware\EnsureTenantSubscriptionActive;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Coolsam\Modules\ModulesPlugin;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Core\Http\Middleware\SetUserLocale;
use Modules\Core\Models\Tenant;
use Modules\Core\Support\Filament\EnabledModuleRegistry;
use Modules\Core\Support\Filament\TenantBrandingResolver;
use Modules\Core\Support\FilamentUi;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $enabledModules = app(EnabledModuleRegistry::class);
        $brandingResolver = app(TenantBrandingResolver::class);

        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->topNavigation(false)
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
            ->colors(fn (): array => $brandingResolver
                ->forTenant(filament()->getTenant())
                ->filamentColors())
            ->brandLogo(fn (): ?string => $brandingResolver
                ->forTenant(filament()->getTenant())
                ->filamentLogoUrl())
            ->navigationGroups($enabledModules->navigationGroups())
            ->tenant(Tenant::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                TabbedDashboard::class,
                BillingPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
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
            ])
            ->userMenuItems([
                Action::make('switch_to_english')
                    ->label('🇬🇧 English')
                    ->icon('heroicon-o-language')
                    ->url(fn () => route('locale.switch', 'en'))
                    ->visible(fn () => FilamentUi::isIndonesian()),
                Action::make('switch_to_indonesian')
                    ->label('🇮🇩 Indonesia')
                    ->icon('heroicon-o-language')
                    ->url(fn () => route('locale.switch', 'id'))
                    ->visible(fn () => ! FilamentUi::isIndonesian()),
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                ModulesPlugin::make(),
            ])
            ->tenantMiddleware([
                SyncShieldTenant::class,
                BindTenantToContainer::class,
            ], isPersistent: true)
            ->renderHook(
                'panels::topbar.start',
                fn () => view('filament.tenant-badge'),
            );

        foreach ($enabledModules->all() as $module) {
            $panel
                ->discoverResources(
                    in: $module->appPath('Filament/Resources'),
                    for: $module->appNamespace('Filament\\Resources'),
                )
                ->discoverPages(
                    in: $module->appPath('Filament/Pages'),
                    for: $module->appNamespace('Filament\\Pages'),
                )
                ->discoverWidgets(
                    in: $module->appPath('Filament/Widgets'),
                    for: $module->appNamespace('Filament\\Widgets'),
                );
        }

        return $panel;
    }
}
