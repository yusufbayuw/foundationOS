<?php

namespace Modules\Core\Filament\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FilamentResourceEagerLoads
{
    /**
     * Eager-load relations commonly rendered in Filament table columns (tenant.name, organization.name).
     *
     * @param  Builder<Model>  $query
     * @param  class-string<Model>  $modelClass
     * @return Builder<Model>
     */
    public static function apply(Builder $query, string $modelClass): Builder
    {
        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            return $query;
        }

        try {
            $model = app($modelClass);
        } catch (\Throwable) {
            return $query;
        }

        if (! $model instanceof Model) {
            return $query;
        }

        $loads = [];

        if ($model->isRelation('tenant')) {
            $loads[] = 'tenant:id,name,code';
        }

        if ($model->isRelation('organization')) {
            $loads[] = 'organization:id,tenant_id,name,code';
        }

        if ($loads === []) {
            return $query;
        }

        return $query->with($loads);
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public static function applyFromModel(Builder $query, Model $model): Builder
    {
        return self::apply($query, $model::class);
    }
}
