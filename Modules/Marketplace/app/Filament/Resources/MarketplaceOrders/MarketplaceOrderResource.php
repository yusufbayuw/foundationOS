<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages\CreateMarketplaceOrder;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages\EditMarketplaceOrder;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages\ListMarketplaceOrders;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages\ViewMarketplaceOrder;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Schemas\MarketplaceOrderForm;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Schemas\MarketplaceOrderInfolist;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Tables\MarketplaceOrdersTable;
use Modules\Marketplace\Models\MarketplaceOrder;

class MarketplaceOrderResource extends ModuleResource
{
    protected static ?string $model = MarketplaceOrder::class;

    public static function form(Schema $schema): Schema
    {
        return MarketplaceOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketplaceOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketplaceOrdersTable::configure($table);
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
            'index' => ListMarketplaceOrders::route('/'),
            'create' => CreateMarketplaceOrder::route('/create'),
            'view' => ViewMarketplaceOrder::route('/{record}'),
            'edit' => EditMarketplaceOrder::route('/{record}/edit'),
        ];
    }
}
