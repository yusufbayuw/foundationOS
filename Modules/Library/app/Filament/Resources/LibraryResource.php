<?php

namespace Modules\Library\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Library\Support\LibraryScopeResolver;

abstract class LibraryResource extends ModuleResource
{
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<Model> $query */
        $query = parent::getEloquentQuery();

        return app(LibraryScopeResolver::class)->apply($query, auth()->user());
    }
}
