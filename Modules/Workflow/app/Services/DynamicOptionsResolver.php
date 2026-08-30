<?php

namespace Modules\Workflow\Services;

use App\Support\CurrentTenant;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use UnitEnum;

class DynamicOptionsResolver
{
    public function resolve(array $optionsSource, int|string|null $tenantId = null): array
    {
        $kind = $optionsSource['kind'] ?? 'static';

        return match ($kind) {
            'static' => $this->resolveStatic($optionsSource),
            'eloquent' => $this->resolveEloquent($optionsSource, $tenantId),
            'enum' => $this->resolveEnum($optionsSource),
            default => throw new WorkflowConfigurationException("Unknown options_source kind: [{$kind}]. Supported: static, eloquent, enum."),
        };
    }

    /**
     * @return array<int|string, string>
     */
    public function resolveForSelect(array $optionsSource, int|string|null $tenantId = null): array
    {
        return collect($this->resolve($optionsSource, $tenantId))
            ->mapWithKeys(fn (array $option): array => [
                $option['value'] => $option['label'],
            ])
            ->all();
    }

    public function resolveLabel(array $optionsSource, mixed $value, int|string|null $tenantId = null): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (($optionsSource['kind'] ?? 'static') === 'eloquent') {
            $modelConfig = $this->resolveEloquentModelConfig($optionsSource);
            $valueColumn = $optionsSource['value'] ?? 'id';
            $labelColumn = $optionsSource['label'] ?? 'name';

            $label = $this->buildEloquentQuery($modelConfig, $optionsSource, $tenantId)
                ->where($valueColumn, $value)
                ->value($labelColumn);

            return $label === null ? null : (string) $label;
        }

        foreach ($this->resolve($optionsSource, $tenantId) as $option) {
            if ((string) $option['value'] === (string) $value) {
                return $option['label'];
            }
        }

        return null;
    }

    private function resolveEloquent(array $source, int|string|null $tenantId): array
    {
        $modelConfig = $this->resolveEloquentModelConfig($source);

        $cacheKey = $this->buildCacheKey($source, $tenantId);
        $ttl = config('workflow-dynamic-sources.cache_ttl_seconds', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($modelConfig, $source, $tenantId) {
            return $this->runEloquentQuery($modelConfig, $source, $tenantId);
        });
    }

    /**
     * @param  array{class: class-string, tenant_relation?: string, tenant_relation_column?: string, tenant_relation_scope?: string}  $modelConfig
     */
    private function runEloquentQuery(array $modelConfig, array $source, int|string|null $tenantId): array
    {
        $maxResults = config('workflow-dynamic-sources.max_results', 500);
        $labelColumn = $source['label'] ?? 'name';
        $valueColumn = $source['value'] ?? 'id';
        $scopeName = $source['scope'] ?? null;
        $query = $this->buildEloquentQuery($modelConfig, $source, $tenantId);

        if ($scopeName && method_exists($modelConfig['class'], 'scope'.ucfirst($scopeName))) {
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

    /**
     * @return array<int, array{value: mixed, label: string}>
     */
    private function resolveStatic(array $source): array
    {
        return collect($source['options'] ?? [])
            ->map(function (mixed $label, mixed $value): array {
                if (is_array($label) && array_key_exists('value', $label)) {
                    return [
                        'value' => $label['value'],
                        'label' => (string) ($label['label'] ?? $label['value']),
                    ];
                }

                return ['value' => $value, 'label' => (string) $label];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{class: class-string, tenant_relation?: string, tenant_relation_column?: string, tenant_relation_scope?: string}
     */
    private function resolveEloquentModelConfig(array $source): array
    {
        $model = $source['model'] ?? null;

        if (! is_string($model) || $model === '') {
            throw new WorkflowConfigurationException('options_source.model is required for kind=eloquent.');
        }

        $allowed = config('workflow-dynamic-sources.models', []);
        $configured = $allowed[$model] ?? null;

        if ($configured === null && in_array($model, $allowed, true)) {
            $configured = $model;
        }

        if ($configured === null) {
            throw new WorkflowConfigurationException("Model [{$model}] is not in the dynamic sources whitelist.");
        }

        if (is_string($configured)) {
            return ['class' => $configured];
        }

        if (! is_array($configured) || ! is_string($configured['class'] ?? null)) {
            throw new WorkflowConfigurationException("Model [{$model}] has an invalid dynamic source configuration.");
        }

        return $configured;
    }

    /**
     * @param  array{class: class-string, tenant_relation?: string, tenant_relation_column?: string, tenant_relation_scope?: string}  $modelConfig
     */
    private function buildEloquentQuery(array $modelConfig, array $source, int|string|null $tenantId): Builder
    {
        $modelClass = $modelConfig['class'];
        $tenantAware = (bool) ($source['tenant_aware'] ?? true);

        if (! $tenantAware) {
            return method_exists($modelClass, 'withoutTenantScope')
                ? $modelClass::withoutTenantScope()
                : $modelClass::query();
        }

        if ($tenantId === null || $tenantId === '') {
            throw new WorkflowConfigurationException('A tenant ID is required for a tenant-aware options source.');
        }

        if (method_exists($modelClass, 'withoutTenantScope')) {
            $currentTenantId = app(CurrentTenant::class)->id();

            if ($currentTenantId !== null && (string) $currentTenantId !== (string) $tenantId) {
                throw new WorkflowConfigurationException('The options source tenant does not match the active tenant.');
            }

            $query = $modelClass::query();

            if ($currentTenantId === null) {
                $query->where('tenant_id', $tenantId);
            }

            return $query;
        }

        $tenantRelation = $modelConfig['tenant_relation'] ?? null;

        if (! is_string($tenantRelation) || $tenantRelation === '') {
            throw new WorkflowConfigurationException("Model [{$modelClass}] cannot be safely scoped to a tenant.");
        }

        $tenantColumn = $modelConfig['tenant_relation_column'] ?? 'tenant_id';
        $tenantScope = $modelConfig['tenant_relation_scope'] ?? null;

        return $modelClass::query()->whereHas(
            $tenantRelation,
            function (Builder $query) use ($tenantColumn, $tenantId, $tenantScope): void {
                if (is_string($tenantScope) && $tenantScope !== '') {
                    $query->{$tenantScope}();
                }

                $query->where($tenantColumn, $tenantId);
            },
        );
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
