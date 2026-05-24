<?php

namespace Modules\Printing\Filament\Resources\Publications;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\Publications\Pages\CreatePublication;
use Modules\Printing\Filament\Resources\Publications\Pages\EditPublication;
use Modules\Printing\Filament\Resources\Publications\Pages\ListPublications;
use Modules\Printing\Filament\Resources\Publications\Pages\ViewPublication;
use Modules\Printing\Filament\Resources\Publications\Schemas\PublicationForm;
use Modules\Printing\Filament\Resources\Publications\Schemas\PublicationInfolist;
use Modules\Printing\Filament\Resources\Publications\Tables\PublicationsTable;
use Modules\Printing\Models\Publication;

class PublicationResource extends ModuleResource
{
    protected static ?string $model = Publication::class;

    public static function form(Schema $schema): Schema
    {
        return PublicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PublicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublicationsTable::configure($table);
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
            'index' => ListPublications::route('/'),
            'create' => CreatePublication::route('/create'),
            'view' => ViewPublication::route('/{record}'),
            'edit' => EditPublication::route('/{record}/edit'),
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
