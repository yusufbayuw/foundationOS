<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\MerchOrder\Filament\Resources\MerchOrders\Pages\CreateMerchOrder;
use Modules\MerchOrder\Filament\Resources\MerchOrders\Pages\EditMerchOrder;
use Modules\MerchOrder\Filament\Resources\MerchOrders\Pages\ListMerchOrders;
use Modules\MerchOrder\Filament\Resources\MerchOrders\Pages\ViewMerchOrder;
use Modules\MerchOrder\Filament\Resources\MerchOrders\Schemas\MerchOrderForm;
use Modules\MerchOrder\Filament\Resources\MerchOrders\Tables\MerchOrdersTable;
use Modules\MerchOrder\Models\MerchOrder;

class MerchOrderResource extends ModuleResource
{
    protected static ?string $model = MerchOrder::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MerchOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MerchOrdersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMerchOrders::route('/'),
            'create' => CreateMerchOrder::route('/create'),
            'view' => ViewMerchOrder::route('/{record}'),
            'edit' => EditMerchOrder::route('/{record}/edit'),
        ];
    }
}
