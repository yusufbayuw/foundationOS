<?php

namespace Modules\Marketplace\Filament\Resources\Sellers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\Sellers\Pages\CreateSeller;
use Modules\Marketplace\Filament\Resources\Sellers\Pages\EditSeller;
use Modules\Marketplace\Filament\Resources\Sellers\Pages\ListSellers;
use Modules\Marketplace\Filament\Resources\Sellers\Pages\ViewSeller;
use Modules\Marketplace\Filament\Resources\Sellers\Schemas\SellerForm;
use Modules\Marketplace\Filament\Resources\Sellers\Schemas\SellerInfolist;
use Modules\Marketplace\Filament\Resources\Sellers\Tables\SellersTable;
use Modules\Marketplace\Models\Seller;

class SellerResource extends ModuleResource
{
    protected static ?string $model = Seller::class;

    public static function form(Schema $schema): Schema
    {
        return SellerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SellerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SellersTable::configure($table);
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
            'index' => ListSellers::route('/'),
            'create' => CreateSeller::route('/create'),
            'view' => ViewSeller::route('/{record}'),
            'edit' => EditSeller::route('/{record}/edit'),
        ];
    }
}
