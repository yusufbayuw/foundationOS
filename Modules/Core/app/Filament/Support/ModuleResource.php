<?php

namespace Modules\Core\Filament\Support;

use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\Guards\GlobalResourceGuard;
use Modules\Core\Filament\Support\Labels\ResourceLabelResolver;
use Modules\Core\Filament\Support\Navigation\ModuleVisibility;
use Modules\Core\Filament\Support\Navigation\NavigationIconResolver;
use Modules\Core\Filament\Support\Navigation\NavigationSortRegistry;
use Modules\Core\Filament\Support\Records\RecordTitleResolver;
use UnitEnum;

abstract class ModuleResource extends Resource
{
    protected static function resolveNavigationIconFromResourceName(): BackedEnum
    {
        return NavigationIconResolver::resolve(static::class);
    }

    public static function isScopedToTenant(): bool
    {
        if (! parent::isScopedToTenant()) {
            return false;
        }

        $modelClass = static::getModel();

        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            return false;
        }

        $ownershipRelationship = static::getTenantOwnershipRelationshipName();

        try {
            $model = app($modelClass);
        } catch (\Throwable) {
            return false;
        }

        return $model instanceof Model
            && $model->isRelation($ownershipRelationship);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        return FilamentResourceEagerLoads::apply($query, static::getModel());
    }

    public static function canCreate(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canCreate();
    }

    public static function canEdit(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canEdit($record);
    }

    public static function canDelete(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canDelete($record);
    }

    public static function canDeleteAny(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canDeleteAny();
    }

    public static function canForceDelete(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canForceDelete($record);
    }

    public static function canForceDeleteAny(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canForceDeleteAny();
    }

    public static function canRestore(Model $record): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canRestore($record);
    }

    public static function canRestoreAny(): bool
    {
        if (static::isGlobalMutationRestricted() && ! static::canCurrentUserMutateGlobalResource()) {
            return false;
        }

        return parent::canRestoreAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ModuleVisibility::shouldRegisterNavigation(static::getModuleName());
    }

    public static function getNavigationSort(): ?int
    {
        return NavigationSortRegistry::sortFor(static::getModuleName(), class_basename(static::class));
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return ResourceLabelResolver::navigationGroup(static::getModuleName());
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$navigationIcon ?? static::resolveNavigationIconFromResourceName();
    }

    public static function getNavigationLabel(): string
    {
        return ResourceLabelResolver::navigationLabel(parent::getPluralModelLabel());
    }

    public static function getModelLabel(): string
    {
        return ResourceLabelResolver::modelLabel(static::getModel());
    }

    public static function getPluralModelLabel(): string
    {
        return ResourceLabelResolver::pluralModelLabel(parent::getPluralModelLabel());
    }

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return RecordTitleResolver::resolve($record);
    }

    protected static function getModuleName(): string
    {
        return str(static::class)->after('Modules\\')->before('\\Filament\\Resources')->beforeLast('\\')->toString();
    }

    /**
     * @return array<string, array<string, int>>
     */
    protected static function navigationSortMap(): array
    {
        return NavigationSortRegistry::map();
    }

    protected static function toProperCase(string $value): string
    {
        return ResourceLabelResolver::toProperCase($value);
    }

    protected static function isGlobalMutationRestricted(): bool
    {
        return GlobalResourceGuard::isMutationRestricted(static::isScopedToTenant());
    }

    protected static function canCurrentUserMutateGlobalResource(): bool
    {
        return GlobalResourceGuard::canCurrentUserMutate();
    }
}
