<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\GoodsReceipts\Pages\CreateGoodsReceipt;
use Modules\Procurement\Filament\Resources\GoodsReceipts\Pages\EditGoodsReceipt;
use Modules\Procurement\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use Modules\Procurement\Filament\Resources\GoodsReceipts\Pages\ViewGoodsReceipt;
use Modules\Procurement\Filament\Resources\GoodsReceipts\Schemas\GoodsReceiptForm;
use Modules\Procurement\Filament\Resources\GoodsReceipts\Schemas\GoodsReceiptInfolist;
use Modules\Procurement\Filament\Resources\GoodsReceipts\Tables\GoodsReceiptsTable;
use Modules\Procurement\Models\GoodsReceipt;

class GoodsReceiptResource extends LocalizedResource
{
    protected static ?string $model = GoodsReceipt::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GoodsReceiptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GoodsReceiptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoodsReceiptsTable::configure($table);
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
            'index' => ListGoodsReceipts::route('/'),
            'create' => CreateGoodsReceipt::route('/create'),
            'view' => ViewGoodsReceipt::route('/{record}'),
            'edit' => EditGoodsReceipt::route('/{record}/edit'),
        ];
    }
}
