<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Filament\Pages\TabbedDashboard;
use App\Http\Middleware\BindTenantToContainer;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Coolsam\Modules\ModulesPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
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
use Modules\Core\Models\Tenant;
use Modules\Core\Support\FilamentUi;
use Nwidart\Modules\Facades\Module;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->topNavigation(false)
            ->login()
            ->profile(EditProfile::class)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->navigationGroups([
                NavigationGroup::make()->label(FilamentUi::module('Core')),
                NavigationGroup::make()->label(FilamentUi::module('Global')),
                NavigationGroup::make()->label(FilamentUi::module('School')),
                NavigationGroup::make()->label(FilamentUi::module('Campus')),
                NavigationGroup::make()->label(FilamentUi::module('Workflow')),
                NavigationGroup::make()->label(FilamentUi::module('Enrollment')),
                NavigationGroup::make()->label(FilamentUi::module('Employee')),
                NavigationGroup::make()->label(FilamentUi::module('Finance')),
                NavigationGroup::make()->label(FilamentUi::module('Procurement')),
                NavigationGroup::make()->label(FilamentUi::module('Library')),
                NavigationGroup::make()->label(FilamentUi::module('Monitoring')),
            ])
            ->tenant(Tenant::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                TabbedDashboard::class,
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

        foreach (Module::allEnabled() as $module) {
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
