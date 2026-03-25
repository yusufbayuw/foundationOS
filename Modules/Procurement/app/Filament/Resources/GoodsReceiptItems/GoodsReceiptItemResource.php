<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages\CreateGoodsReceiptItem;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages\EditGoodsReceiptItem;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages\ListGoodsReceiptItems;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages\ViewGoodsReceiptItem;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\Schemas\GoodsReceiptItemForm;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\Schemas\GoodsReceiptItemInfolist;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\Tables\GoodsReceiptItemsTable;
use Modules\Procurement\Models\GoodsReceiptItem;

class GoodsReceiptItemResource extends LocalizedResource
{
    protected static ?string $model = GoodsReceiptItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GoodsReceiptItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GoodsReceiptItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoodsReceiptItemsTable::configure($table);
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
            'index' => ListGoodsReceiptItems::route('/'),
            'create' => CreateGoodsReceiptItem::route('/create'),
            'view' => ViewGoodsReceiptItem::route('/{record}'),
            'edit' => EditGoodsReceiptItem::route('/{record}/edit'),
        ];
    }
}
