<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages\CreateMarketplaceOrderItem;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages\EditMarketplaceOrderItem;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages\ListMarketplaceOrderItems;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages\ViewMarketplaceOrderItem;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Schemas\MarketplaceOrderItemForm;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Schemas\MarketplaceOrderItemInfolist;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Tables\MarketplaceOrderItemsTable;
use Modules\Marketplace\Models\MarketplaceOrderItem;

class MarketplaceOrderItemResource extends ModuleResource
{
    protected static ?string $model = MarketplaceOrderItem::class;

    public static function form(Schema $schema): Schema
    {
        return MarketplaceOrderItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketplaceOrderItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketplaceOrderItemsTable::configure($table);
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
            'index' => ListMarketplaceOrderItems::route('/'),
            'create' => CreateMarketplaceOrderItem::route('/create'),
            'view' => ViewMarketplaceOrderItem::route('/{record}'),
            'edit' => EditMarketplaceOrderItem::route('/{record}/edit'),
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
