<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Pages\CreateMarketplaceOrderShipment;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Pages\EditMarketplaceOrderShipment;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Pages\ListMarketplaceOrderShipments;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Pages\ViewMarketplaceOrderShipment;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Schemas\MarketplaceOrderShipmentForm;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Schemas\MarketplaceOrderShipmentInfolist;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Tables\MarketplaceOrderShipmentsTable;
use Modules\Marketplace\Models\MarketplaceOrderShipment;

class MarketplaceOrderShipmentResource extends ModuleResource
{
    protected static ?string $model = MarketplaceOrderShipment::class;

    public static function form(Schema $schema): Schema
    {
        return MarketplaceOrderShipmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketplaceOrderShipmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketplaceOrderShipmentsTable::configure($table);
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
            'index' => ListMarketplaceOrderShipments::route('/'),
            'create' => CreateMarketplaceOrderShipment::route('/create'),
            'view' => ViewMarketplaceOrderShipment::route('/{record}'),
            'edit' => EditMarketplaceOrderShipment::route('/{record}/edit'),
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
