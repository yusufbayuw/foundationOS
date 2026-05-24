<?php

namespace Modules\Cms\Filament\Resources\CmsMedia;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\CmsMedia\Pages\CreateCmsMedia;
use Modules\Cms\Filament\Resources\CmsMedia\Pages\EditCmsMedia;
use Modules\Cms\Filament\Resources\CmsMedia\Pages\ListCmsMedia;
use Modules\Cms\Filament\Resources\CmsMedia\Pages\ViewCmsMedia;
use Modules\Cms\Filament\Resources\CmsMedia\Schemas\CmsMediaForm;
use Modules\Cms\Filament\Resources\CmsMedia\Schemas\CmsMediaInfolist;
use Modules\Cms\Filament\Resources\CmsMedia\Tables\CmsMediaTable;
use Modules\Cms\Models\CmsMedia;
use Modules\Core\Filament\Support\ModuleResource;

class CmsMediaResource extends ModuleResource
{
    protected static ?string $model = CmsMedia::class;

    public static function form(Schema $schema): Schema
    {
        return CmsMediaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CmsMediaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CmsMediaTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCmsMedia::route('/'),
            'create' => CreateCmsMedia::route('/create'),
            'view' => ViewCmsMedia::route('/{record}'),
            'edit' => EditCmsMedia::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
