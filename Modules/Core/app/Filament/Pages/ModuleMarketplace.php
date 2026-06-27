<?php

namespace Modules\Core\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Module;
use Modules\Core\Models\TenantModule;
use Modules\Core\Support\FilamentUi;

class ModuleMarketplace extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::PuzzlePiece;

    protected string $view = 'core::filament.pages.module-marketplace';

    protected static ?int $navigationSort = 5;

    public function getTitle(): string
    {
        return FilamentUi::text('Module Marketplace');
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Module Marketplace');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Core');
    }

    public function toggleModule(int $moduleId): void
    {
        $tenant = current_tenant_model();

        if (! $tenant) {
            return;
        }

        $module = Module::findOrFail($moduleId);

        /** @var TenantModule|null $tenantModule */
        $tenantModule = TenantModule::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('module_id', $moduleId)
            ->first();

        if ($tenantModule) {
            $tenantModule->update([
                'is_enabled' => ! $tenantModule->is_enabled,
                'enabled_at' => ! $tenantModule->is_enabled ? now() : $tenantModule->enabled_at,
                'disabled_at' => $tenantModule->is_enabled ? now() : null,
            ]);
        } else {
            TenantModule::create([
                'tenant_id' => $tenant->getKey(),
                'module_id' => $moduleId,
                'is_enabled' => true,
                'enabled_at' => now(),
            ]);
        }

        Cache::forget("tenant_module_active:{$tenant->getKey()}:{$module->code}");

        Notification::make()
            ->title(FilamentUi::text('Module updated'))
            ->success()
            ->send();
    }

    public function getModulesWithStatus(): Collection
    {
        $tenant = current_tenant_model();

        $enabledModuleIds = $tenant
            ? TenantModule::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('is_enabled', true)
                ->pluck('module_id')
                ->toArray()
            : [];

        return Module::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->each(function (Module $module) use ($enabledModuleIds): void {
                $module->is_tenant_enabled = $module->is_core || in_array($module->id, $enabledModuleIds, true);
            });
    }
}
