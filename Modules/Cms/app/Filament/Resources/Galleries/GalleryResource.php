<?php

namespace Modules\Cms\Filament\Resources\Galleries;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\Galleries\Pages\CreateGallery;
use Modules\Cms\Filament\Resources\Galleries\Pages\EditGallery;
use Modules\Cms\Filament\Resources\Galleries\Pages\ListGalleries;
use Modules\Cms\Filament\Resources\Galleries\Pages\ViewGallery;
use Modules\Cms\Filament\Resources\Galleries\Schemas\GalleryForm;
use Modules\Cms\Filament\Resources\Galleries\Schemas\GalleryInfolist;
use Modules\Cms\Filament\Resources\Galleries\Tables\GalleriesTable;
use Modules\Cms\Models\Gallery;
use Modules\Core\Filament\Support\ModuleResource;

class GalleryResource extends ModuleResource
{
    protected static ?string $model = Gallery::class;

    public static function form(Schema $schema): Schema
    {
        return GalleryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GalleryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleriesTable::configure($table);
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
            'index' => ListGalleries::route('/'),
            'create' => CreateGallery::route('/create'),
            'view' => ViewGallery::route('/{record}'),
            'edit' => EditGallery::route('/{record}/edit'),
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
