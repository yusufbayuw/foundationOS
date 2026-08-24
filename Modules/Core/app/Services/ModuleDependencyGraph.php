<?php

namespace Modules\Core\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LogicException;
use Modules\Core\Models\Module;

class ModuleDependencyGraph
{
    /**
     * @param  Collection<int, Module>  $availableModules
     * @param  list<string>  $requestedModuleCodes
     * @return array{resolved: list<string>, missing: list<string>, cycles: list<string>}
     */
    public function inspect(Collection $availableModules, array $requestedModuleCodes): array
    {
        $modulesByCode = $availableModules->keyBy(
            fn (Module $module): string => Str::lower($module->code),
        );
        $resolvedModuleCodes = [];
        $resolvingModuleCodes = [];
        $missingModuleCodes = [];
        $cycleModuleCodes = [];

        $resolveModule = function (string $moduleCode) use (
            &$resolveModule,
            &$resolvedModuleCodes,
            &$resolvingModuleCodes,
            &$missingModuleCodes,
            &$cycleModuleCodes,
            $modulesByCode,
        ): void {
            $moduleCode = Str::lower($moduleCode);

            if (isset($resolvedModuleCodes[$moduleCode])) {
                return;
            }

            if (isset($resolvingModuleCodes[$moduleCode])) {
                $cycleModuleCodes[$moduleCode] = true;

                return;
            }

            /** @var Module|null $module */
            $module = $modulesByCode->get($moduleCode);

            if ($module === null) {
                $missingModuleCodes[$moduleCode] = true;

                return;
            }

            $resolvingModuleCodes[$moduleCode] = true;

            foreach ($module->required_modules ?? [] as $requiredModuleCode) {
                if (is_string($requiredModuleCode)) {
                    $resolveModule($requiredModuleCode);
                }
            }

            unset($resolvingModuleCodes[$moduleCode]);
            $resolvedModuleCodes[$moduleCode] = true;
        };

        foreach ($requestedModuleCodes as $moduleCode) {
            $resolveModule($moduleCode);
        }

        return [
            'resolved' => array_keys($resolvedModuleCodes),
            'missing' => array_keys($missingModuleCodes),
            'cycles' => array_keys($cycleModuleCodes),
        ];
    }

    /**
     * @param  Collection<int, Module>  $availableModules
     * @param  list<string>  $requestedModuleCodes
     * @return list<string>
     */
    public function resolveOrFail(Collection $availableModules, array $requestedModuleCodes): array
    {
        $inspection = $this->inspect($availableModules, $requestedModuleCodes);

        if ($inspection['cycles'] !== []) {
            throw new LogicException(
                'Circular module dependency detected at ['.implode(', ', $inspection['cycles']).'].',
            );
        }

        if ($inspection['missing'] !== []) {
            throw new LogicException(
                'Required modules are not active or installed: '.implode(', ', $inspection['missing']).'.',
            );
        }

        return $inspection['resolved'];
    }
}
