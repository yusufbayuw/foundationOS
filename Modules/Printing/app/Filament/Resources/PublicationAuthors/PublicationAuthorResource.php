<?php

namespace Modules\Printing\Filament\Resources\PublicationAuthors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\PublicationAuthors\Pages\CreatePublicationAuthor;
use Modules\Printing\Filament\Resources\PublicationAuthors\Pages\EditPublicationAuthor;
use Modules\Printing\Filament\Resources\PublicationAuthors\Pages\ListPublicationAuthors;
use Modules\Printing\Filament\Resources\PublicationAuthors\Pages\ViewPublicationAuthor;
use Modules\Printing\Filament\Resources\PublicationAuthors\Schemas\PublicationAuthorForm;
use Modules\Printing\Filament\Resources\PublicationAuthors\Schemas\PublicationAuthorInfolist;
use Modules\Printing\Filament\Resources\PublicationAuthors\Tables\PublicationAuthorsTable;
use Modules\Printing\Models\PublicationAuthor;

class PublicationAuthorResource extends ModuleResource
{
    protected static ?string $model = PublicationAuthor::class;

    public static function form(Schema $schema): Schema
    {
        return PublicationAuthorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PublicationAuthorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublicationAuthorsTable::configure($table);
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
            'index' => ListPublicationAuthors::route('/'),
            'create' => CreatePublicationAuthor::route('/create'),
            'view' => ViewPublicationAuthor::route('/{record}'),
            'edit' => EditPublicationAuthor::route('/{record}/edit'),
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
