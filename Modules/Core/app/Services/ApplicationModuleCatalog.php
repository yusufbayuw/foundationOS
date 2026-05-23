<?php

namespace Modules\Core\Services;

use Illuminate\Support\Str;
use Modules\Core\Models\Module;
use Modules\Core\Support\FilamentUi;
use Nwidart\Modules\Facades\Module as NwidartModule;

class ApplicationModuleCatalog
{
    /** @var array<string, array{is_core: bool, sort_order: int}> */
    private const MODULE_DEFAULTS = [
        'core' => ['is_core' => true, 'sort_order' => 0],
        'global' => ['is_core' => true, 'sort_order' => 1],
        'school' => ['is_core' => false, 'sort_order' => 10],
        'campus' => ['is_core' => false, 'sort_order' => 20],
        'workflow' => ['is_core' => false, 'sort_order' => 30],
        'enrollment' => ['is_core' => false, 'sort_order' => 40],
        'employee' => ['is_core' => false, 'sort_order' => 50],
        'finance' => ['is_core' => false, 'sort_order' => 60],
        'procurement' => ['is_core' => false, 'sort_order' => 70],
        'library' => ['is_core' => false, 'sort_order' => 80],
        'monitoring' => ['is_core' => false, 'sort_order' => 90],
    ];

    /**
     * Sync the commercial module catalog from enabled nwidart modules.
     */
    public function sync(): void
    {
        $sortOrder = 0;

        foreach (NwidartModule::allEnabled() as $nwidartModule) {
            $name = $nwidartModule->getName();
            $code = strtolower($name);
            $defaults = self::MODULE_DEFAULTS[$code] ?? ['is_core' => false, 'sort_order' => 100 + $sortOrder];

            Module::query()->updateOrCreate(
                ['code' => $code],
                [
                    'slug' => $nwidartModule->getLowerName(),
                    'name' => FilamentUi::module($name),
                    'description' => $nwidartModule->getDescription() ?: null,
                    'is_core' => $defaults['is_core'],
                    'is_active' => true,
                    'is_premium' => false,
                    'sort_order' => $defaults['sort_order'],
                ],
            );

            $sortOrder++;
        }
    }

    /**
     * @return list<string> PascalCase module names used in navigation cache keys.
     */
    public function enabledNavigationModuleNames(): array
    {
        return Module::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->map(fn (string $code): string => Str::studly($code))
            ->values()
            ->all();
    }
}
