<?php

namespace Modules\Printing\Filament\Resources\Royalties;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\Royalties\Pages\CreateRoyalty;
use Modules\Printing\Filament\Resources\Royalties\Pages\EditRoyalty;
use Modules\Printing\Filament\Resources\Royalties\Pages\ListRoyalties;
use Modules\Printing\Filament\Resources\Royalties\Pages\ViewRoyalty;
use Modules\Printing\Filament\Resources\Royalties\Schemas\RoyaltyForm;
use Modules\Printing\Filament\Resources\Royalties\Schemas\RoyaltyInfolist;
use Modules\Printing\Filament\Resources\Royalties\Tables\RoyaltiesTable;
use Modules\Printing\Models\Royalty;

class RoyaltyResource extends ModuleResource
{
    protected static ?string $model = Royalty::class;

    public static function form(Schema $schema): Schema
    {
        return RoyaltyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoyaltyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoyaltiesTable::configure($table);
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
            'index' => ListRoyalties::route('/'),
            'create' => CreateRoyalty::route('/create'),
            'view' => ViewRoyalty::route('/{record}'),
            'edit' => EditRoyalty::route('/{record}/edit'),
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
