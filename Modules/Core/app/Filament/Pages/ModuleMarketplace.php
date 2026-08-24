<?php

namespace Modules\Core\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Filament\Support\Navigation\ModuleVisibility;
use Modules\Core\Models\Module;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;
use Modules\Core\Services\ProductProfileCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
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

    public static function canAccess(): bool
    {
        $tenant = Filament::getTenant();
        $user = Filament::auth()->user();

        return $tenant instanceof Tenant
            && $user instanceof User
            && $user->canAccessTenant($tenant)
            && $user->isTenantAdministrator($tenant);
    }

    public function toggleModule(int $moduleId, TenantModuleProvisioner $provisioner): void
    {
        $tenant = Filament::getTenant();

        abort_unless($tenant instanceof Tenant && static::canAccess(), 403);

        $module = Module::query()
            ->where('is_active', true)
            ->findOrFail($moduleId);

        /** @var TenantModule|null $tenantModule */
        $tenantModule = TenantModule::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('module_id', $moduleId)
            ->first();

        if (! $tenantModule?->is_enabled) {
            if ($module->is_premium && ! $this->isPremiumModuleIncluded($tenant, $module)) {
                Notification::make()
                    ->title('Modul premium belum termasuk dalam paket')
                    ->danger()
                    ->send();

                return;
            }

            DB::transaction(fn () => $provisioner->enableForTenant($tenant, [Str::lower($module->code)]));
        } else {
            if ($this->isProtectedModule($tenant, $module)) {
                Notification::make()
                    ->title('Modul wajib tidak dapat dinonaktifkan')
                    ->warning()
                    ->send();

                return;
            }

            $dependentModuleNames = $this->enabledDependentModuleNames($tenant, $module);

            if ($dependentModuleNames !== []) {
                Notification::make()
                    ->title('Modul masih dibutuhkan')
                    ->body('Nonaktifkan lebih dahulu: '.implode(', ', $dependentModuleNames).'.')
                    ->warning()
                    ->send();

                return;
            }

            DB::transaction(fn () => $tenantModule->update([
                'is_enabled' => false,
                'disabled_at' => now(),
            ]));
        }

        Cache::forget(ModuleVisibility::cacheKey($tenant->getKey(), Str::studly($module->code)));

        Notification::make()
            ->title(FilamentUi::text('Module updated'))
            ->success()
            ->send();
    }

    protected function isProtectedModule(Tenant $tenant, Module $module): bool
    {
        if ($module->is_core) {
            return true;
        }

        $profileCode = (string) ($tenant->product_profile_code ?? '');
        $catalog = app(ProductProfileCatalog::class);

        return $profileCode !== ''
            && $catalog->exists($profileCode)
            && in_array(Str::lower($module->code), $catalog->moduleCodes($profileCode), true);
    }

    /** @return list<string> */
    protected function enabledDependentModuleNames(Tenant $tenant, Module $module): array
    {
        $moduleCode = Str::lower($module->code);

        return TenantModule::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('is_enabled', true)
            ->with('module:id,name,required_modules')
            ->get()
            ->pluck('module')
            ->filter(fn (?Module $candidate): bool => $candidate !== null
                && in_array($moduleCode, array_map(fn (string $code): string => Str::lower($code), $candidate->required_modules ?? []), true))
            ->pluck('name')
            ->values()
            ->all();
    }

    protected function isPremiumModuleIncluded(Tenant $tenant, Module $module): bool
    {
        $includedModules = collect($tenant->subscriptionPlan?->included_modules ?? [])
            ->map(fn (mixed $includedModule): ?string => match (true) {
                is_string($includedModule) => Str::lower($includedModule),
                is_array($includedModule) && is_string($includedModule['code'] ?? null) => Str::lower($includedModule['code']),
                default => null,
            })
            ->filter()
            ->all();

        return in_array(Str::lower($module->code), $includedModules, true);
    }

    public function getModulesWithStatus(): Collection
    {
        $tenant = Filament::getTenant();

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
            ->each(function (Module $module) use ($enabledModuleIds, $tenant): void {
                $module->is_tenant_enabled = $module->is_core || in_array($module->id, $enabledModuleIds, true);
                $module->can_toggle = $tenant instanceof Tenant && ! $this->isProtectedModule($tenant, $module);
            });
    }
}
