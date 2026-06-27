<?php

namespace Modules\Core\Services;

use App\Support\TypedValue;
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
        'inventory' => ['is_core' => false, 'sort_order' => 95],
        'sales' => ['is_core' => false, 'sort_order' => 96],
        'legal' => ['is_core' => false, 'sort_order' => 100],
        'asset' => ['is_core' => false, 'sort_order' => 101],
        'dms' => ['is_core' => false, 'sort_order' => 102],
        'helpdesk' => ['is_core' => false, 'sort_order' => 103],
        'facility' => ['is_core' => false, 'sort_order' => 104],
        'eoffice' => ['is_core' => false, 'sort_order' => 105],
        'itops' => ['is_core' => false, 'sort_order' => 106],
        'transport' => ['is_core' => false, 'sort_order' => 107],
        'boarding' => ['is_core' => false, 'sort_order' => 108],
        'cafeteria' => ['is_core' => false, 'sort_order' => 109],
        'physicalsecurity' => ['is_core' => false, 'sort_order' => 110],
        'counseling' => ['is_core' => false, 'sort_order' => 111],
        'clinic' => ['is_core' => false, 'sort_order' => 112],
        'event' => ['is_core' => false, 'sort_order' => 113],
        'merchorder' => ['is_core' => false, 'sort_order' => 114],
        'alumni' => ['is_core' => false, 'sort_order' => 115],
        'cms' => ['is_core' => false, 'sort_order' => 116],
        'donation' => ['is_core' => false, 'sort_order' => 117],
        'training' => ['is_core' => false, 'sort_order' => 118],
        'risk' => ['is_core' => false, 'sort_order' => 119],
        'internalaudit' => ['is_core' => false, 'sort_order' => 120],
        'isocompliance' => ['is_core' => false, 'sort_order' => 121],
        'educationqa' => ['is_core' => false, 'sort_order' => 122],
        'kpienterprise' => ['is_core' => false, 'sort_order' => 123],
        'capacity' => ['is_core' => false, 'sort_order' => 124],
        'ai' => ['is_core' => false, 'sort_order' => 125],
        'messaging' => ['is_core' => false, 'sort_order' => 126],
    ];

    /**
     * Sync the commercial module catalog from enabled nwidart modules.
     */
    public function sync(): void
    {
        $sortOrder = 0;

        foreach (NwidartModule::allEnabled() as $nwidartModule) {
            if (
                ! is_object($nwidartModule) ||
                ! method_exists($nwidartModule, 'getName') ||
                ! method_exists($nwidartModule, 'getLowerName') ||
                ! method_exists($nwidartModule, 'getDescription')
            ) {
                continue;
            }

            $name = TypedValue::string($nwidartModule->getName());
            $code = strtolower($name);
            $defaults = self::MODULE_DEFAULTS[$code] ?? ['is_core' => false, 'sort_order' => 100 + $sortOrder];

            Module::query()->updateOrCreate(
                ['code' => $code],
                [
                    'slug' => $nwidartModule->getLowerName(),
                    'name' => FilamentUi::module($name),
                    'description' => TypedValue::string($nwidartModule->getDescription()) ?: null,
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
     * @return list<string>
     */
    public function enabledNavigationModuleNames(): array
    {
        return array_values(Module::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->map(fn (mixed $code): string => Str::studly(TypedValue::string($code)))
            ->all());
    }
}
