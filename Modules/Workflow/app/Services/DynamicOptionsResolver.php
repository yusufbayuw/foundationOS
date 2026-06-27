<?php

namespace Modules\Workflow\Services;

use App\Support\CurrentTenant;
use App\Support\TypedValue;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use UnitEnum;

class DynamicOptionsResolver
{
    /**
     * @param  array<string, mixed>  $optionsSource
     * @return list<array{value: mixed, label: string}>
     */
    public function resolve(array $optionsSource, int|string|null $tenantId = null): array
    {
        $kind = TypedValue::string($optionsSource['kind'] ?? 'static', 'static');

        return match ($kind) {
            'eloquent' => $this->resolveEloquent($optionsSource, $tenantId),
            'enum' => $this->resolveEnum($optionsSource),
            default => throw new WorkflowConfigurationException("Unknown options_source kind: [{$kind}]. Supported: eloquent, enum."),
        };
    }

    /**
     * @param  array<string, mixed>  $source
     * @return list<array{value: mixed, label: string}>
     */
    private function resolveEloquent(array $source, int|string|null $tenantId): array
    {
        $modelClass = $source['model'] ?? null;

        if (! is_string($modelClass) || $modelClass === '') {
            throw new WorkflowConfigurationException('options_source.model is required for kind=eloquent.');
        }

        /** @var array<string, class-string<Model>>|list<class-string<Model>> $allowed */
        $allowed = config('workflow-dynamic-sources.models', []);

        if (! array_key_exists($modelClass, $allowed) && ! in_array($modelClass, $allowed, true)) {
            throw new WorkflowConfigurationException("Model [{$modelClass}] is not in the dynamic sources whitelist.");
        }

        $resolvedClass = is_string($allowed[$modelClass] ?? null) ? $allowed[$modelClass] : $modelClass;

        if (! is_subclass_of($resolvedClass, Model::class)) {
            throw new WorkflowConfigurationException("Model [{$modelClass}] is not a valid Eloquent model.");
        }

        $cacheKey = $this->buildCacheKey($source, $tenantId);
        $ttl = TypedValue::int(config('workflow-dynamic-sources.cache_ttl_seconds'), 300);

        /** @var list<array{value: mixed, label: string}> $options */
        $options = Cache::remember($cacheKey, $ttl, function () use ($resolvedClass, $source, $tenantId): array {
            return $this->runEloquentQuery($resolvedClass, $source, $tenantId);
        });

        return $options;
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $source
     * @return list<array{value: mixed, label: string}>
     */
    private function runEloquentQuery(string $modelClass, array $source, int|string|null $tenantId): array
    {
        $maxResults = TypedValue::int(config('workflow-dynamic-sources.max_results'), 500);
        $labelColumn = TypedValue::string($source['label'] ?? 'name', 'name');
        $valueColumn = TypedValue::string($source['value'] ?? 'id', 'id');
        $scopeName = isset($source['scope']) && is_string($source['scope']) ? $source['scope'] : null;
        $tenantAware = (bool) ($source['tenant_aware'] ?? true);

        /** @var Builder<Model> $query */
        $query = $modelClass::query();

        if ($tenantAware && $tenantId !== null && method_exists($modelClass, 'withoutTenantScope')) {
            if (! app(CurrentTenant::class)->id()) {
                $query->where($query->getModel()->qualifyColumn('tenant_id'), $tenantId);
            }
        } elseif (! $tenantAware && method_exists($modelClass, 'withoutTenantScope')) {
            /** @var Builder<Model> $query */
            $query = $modelClass::withoutTenantScope();
        }

        if ($scopeName !== null && method_exists($modelClass, 'scope'.ucfirst($scopeName))) {
            $query->{$scopeName}();
        }

        $searchField = TypedValue::string($source['search_field'] ?? $labelColumn, $labelColumn);
        $search = $source['search'] ?? null;

        if (is_string($search) && $search !== '') {
            $query->where($searchField, 'like', "%{$search}%");
        }

        /** @var list<array{value: mixed, label: string}> $plucked */
        $plucked = $query
            ->limit($maxResults)
            ->pluck($labelColumn, $valueColumn)
            ->map(fn (mixed $label, mixed $value): array => [
                'value' => $value,
                'label' => TypedValue::string($label),
            ])
            ->values()
            ->all();

        return $plucked;
    }

    /**
     * @param  array<string, mixed>  $source
     * @return list<array{value: int|string, label: string}>
     */
    private function resolveEnum(array $source): array
    {
        $class = $source['class'] ?? null;

        if (! is_string($class) || $class === '' || ! enum_exists($class)) {
            throw new WorkflowConfigurationException('options_source.class ['.TypedValue::string($class).'] is not a valid enum.');
        }

        $cases = $class::cases();

        return array_map(function (UnitEnum $case): array {
            $value = $case instanceof BackedEnum ? $case->value : $case->name;
            $label = method_exists($case, 'label') ? TypedValue::string($case->label()) : $case->name;

            return ['value' => $value, 'label' => $label];
        }, $cases);
    }

    /**
     * @param  array<string, mixed>  $source
     */
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

        return 'workflow_dyn_opts_t'.($tenantId ?? 'global').'_'.md5(is_string($signature) ? $signature : '');
    }
}
