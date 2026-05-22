<?php

namespace Modules\Workflow\Services;

use App\Support\CurrentTenant;
use BackedEnum;
use Illuminate\Support\Facades\Cache;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use UnitEnum;

class DynamicOptionsResolver
{
    public function resolve(array $optionsSource, int|string|null $tenantId = null): array
    {
        $kind = $optionsSource['kind'] ?? 'static';

        return match ($kind) {
            'eloquent' => $this->resolveEloquent($optionsSource, $tenantId),
            'enum' => $this->resolveEnum($optionsSource),
            default => throw new WorkflowConfigurationException("Unknown options_source kind: [{$kind}]. Supported: eloquent, enum."),
        };
    }

    private function resolveEloquent(array $source, int|string|null $tenantId): array
    {
        $modelClass = $source['model'] ?? null;

        if (! $modelClass) {
            throw new WorkflowConfigurationException('options_source.model is required for kind=eloquent.');
        }

        $allowed = config('workflow-dynamic-sources.models', []);

        if (! array_key_exists($modelClass, $allowed) && ! in_array($modelClass, $allowed, true)) {
            throw new WorkflowConfigurationException("Model [{$modelClass}] is not in the dynamic sources whitelist.");
        }

        $resolvedClass = $allowed[$modelClass] ?? $modelClass;

        $cacheKey = $this->buildCacheKey($source, $tenantId);
        $ttl = config('workflow-dynamic-sources.cache_ttl_seconds', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($resolvedClass, $source, $tenantId) {
            return $this->runEloquentQuery($resolvedClass, $source, $tenantId);
        });
    }

    private function runEloquentQuery(string $modelClass, array $source, int|string|null $tenantId): array
    {
        $maxResults = config('workflow-dynamic-sources.max_results', 500);
        $labelColumn = $source['label'] ?? 'name';
        $valueColumn = $source['value'] ?? 'id';
        $scopeName = $source['scope'] ?? null;
        $tenantAware = (bool) ($source['tenant_aware'] ?? true);

        $query = $modelClass::query();

        if ($tenantAware && $tenantId !== null && method_exists($modelClass, 'withoutTenantScope')) {
            // Model uses BelongsToTenant — let the global scope apply naturally
            // by ensuring tenant context is set; otherwise fall back to explicit filter.
            if (! app(CurrentTenant::class)->id()) {
                $query->where('tenant_id', $tenantId);
            }
        } elseif (! $tenantAware) {
            // Bypass tenant scope for global reference tables.
            if (method_exists($modelClass, 'withoutTenantScope')) {
                $query = $modelClass::withoutTenantScope();
            }
        }

        if ($scopeName && method_exists($modelClass, 'scope'.ucfirst($scopeName))) {
            $query->{$scopeName}();
        }

        $searchField = $source['search_field'] ?? $labelColumn;
        $search = $source['search'] ?? null;

        if ($search !== null && $search !== '') {
            $query->where($searchField, 'like', "%{$search}%");
        }

        return $query
            ->limit($maxResults)
            ->pluck($labelColumn, $valueColumn)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => (string) $label])
            ->values()
            ->all();
    }

    private function resolveEnum(array $source): array
    {
        $class = $source['class'] ?? null;

        if (! $class || ! enum_exists($class)) {
            throw new WorkflowConfigurationException("options_source.class [{$class}] is not a valid enum.");
        }

        $cases = $class::cases();

        return array_map(function (UnitEnum $case) {
            $value = $case instanceof BackedEnum ? $case->value : $case->name;
            $label = method_exists($case, 'label') ? $case->label() : $case->name;

            return ['value' => $value, 'label' => $label];
        }, $cases);
    }

    private function buildCacheKey(array $source, int|string|null $tenantId): string
    {
        $signature = json_encode([
            'model' => $source['model'] ?? '',
            'scope' => $source['scope'] ?? null,
            'tenant_aware' => $source['tenant_aware'] ?? true,
            'label' => $source['label'] ?? 'name',
            'value' => $source['value'] ?? 'id',
            'search' => $source['search'] ?? null,
        ]);

        return 'workflow_dyn_opts_t'.($tenantId ?? 'global').'_'.md5($signature);
    }
}
