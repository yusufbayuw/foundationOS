<?php

namespace Modules\Library\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Library\Support\LibraryScopeResolver;

abstract class LibraryResource extends ModuleResource
{
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder $query */
        $query = parent::getEloquentQuery();

        return app(LibraryScopeResolver::class)->apply($query, auth()->user());
    }
}
