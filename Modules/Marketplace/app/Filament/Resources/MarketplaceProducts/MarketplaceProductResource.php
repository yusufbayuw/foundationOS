<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProducts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\Pages\CreateMarketplaceProduct;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\Pages\EditMarketplaceProduct;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\Pages\ListMarketplaceProducts;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\Pages\ViewMarketplaceProduct;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\Schemas\MarketplaceProductForm;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\Schemas\MarketplaceProductInfolist;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\Tables\MarketplaceProductsTable;
use Modules\Marketplace\Models\MarketplaceProduct;

class MarketplaceProductResource extends ModuleResource
{
    protected static ?string $model = MarketplaceProduct::class;

    public static function form(Schema $schema): Schema
    {
        return MarketplaceProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketplaceProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketplaceProductsTable::configure($table);
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
            'index' => ListMarketplaceProducts::route('/'),
            'create' => CreateMarketplaceProduct::route('/create'),
            'view' => ViewMarketplaceProduct::route('/{record}'),
            'edit' => EditMarketplaceProduct::route('/{record}/edit'),
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
