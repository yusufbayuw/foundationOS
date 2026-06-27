<?php

namespace App\Providers\Filament\Admin;

use App\Filament\Pages\BillingPage;
use App\Filament\Pages\TabbedDashboard;
use App\Http\Middleware\BindTenantToContainer;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Filament\Panel;
use Modules\Core\Support\Filament\EnabledModuleRegistry;

class AdminPanelNavigationConfigurator
{
    public function __construct(
        private readonly EnabledModuleRegistry $modules,
    ) {}

    public function configure(Panel $panel): Panel
    {
        return $panel
            ->topNavigation(false)
            ->navigationGroups($this->modules->navigationGroups())
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->pages([
                TabbedDashboard::class,
                BillingPage::class,
            ])
            ->widgets([])
            ->tenantMiddleware([
                SyncShieldTenant::class,
                BindTenantToContainer::class,
            ], isPersistent: true)
            ->renderHook(
                'panels::topbar.start',
                fn () => view('filament.tenant-badge'),
            );
    }
}
