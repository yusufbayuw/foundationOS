<?php

namespace Modules\Cafeteria\Filament\Resources\MenuStocks;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cafeteria\Filament\Resources\MenuStocks\Pages\CreateMenuStock;
use Modules\Cafeteria\Filament\Resources\MenuStocks\Pages\EditMenuStock;
use Modules\Cafeteria\Filament\Resources\MenuStocks\Pages\ListMenuStocks;
use Modules\Cafeteria\Filament\Resources\MenuStocks\Pages\ViewMenuStock;
use Modules\Cafeteria\Filament\Resources\MenuStocks\Schemas\MenuStockForm;
use Modules\Cafeteria\Filament\Resources\MenuStocks\Schemas\MenuStockInfolist;
use Modules\Cafeteria\Filament\Resources\MenuStocks\Tables\MenuStocksTable;
use Modules\Cafeteria\Models\MenuStock;
use Modules\Core\Filament\Support\ModuleResource;

class MenuStockResource extends ModuleResource
{
    protected static ?string $model = MenuStock::class;

    public static function form(Schema $schema): Schema
    {
        return MenuStockForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MenuStockInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenuStocksTable::configure($table);
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
            'index' => ListMenuStocks::route('/'),
            'create' => CreateMenuStock::route('/create'),
            'view' => ViewMenuStock::route('/{record}'),
            'edit' => EditMenuStock::route('/{record}/edit'),
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
