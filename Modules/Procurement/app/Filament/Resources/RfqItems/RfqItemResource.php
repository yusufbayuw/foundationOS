<?php

namespace Modules\Procurement\Filament\Resources\RfqItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Procurement\Filament\Resources\RfqItems\Pages\CreateRfqItem;
use Modules\Procurement\Filament\Resources\RfqItems\Pages\EditRfqItem;
use Modules\Procurement\Filament\Resources\RfqItems\Pages\ListRfqItems;
use Modules\Procurement\Filament\Resources\RfqItems\Pages\ViewRfqItem;
use Modules\Procurement\Filament\Resources\RfqItems\Schemas\RfqItemForm;
use Modules\Procurement\Filament\Resources\RfqItems\Schemas\RfqItemInfolist;
use Modules\Procurement\Filament\Resources\RfqItems\Tables\RfqItemsTable;
use Modules\Procurement\Models\RfqItem;

class RfqItemResource extends LocalizedResource
{
    protected static ?string $model = RfqItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RfqItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RfqItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RfqItemsTable::configure($table);
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
            'index' => ListRfqItems::route('/'),
            'create' => CreateRfqItem::route('/create'),
            'view' => ViewRfqItem::route('/{record}'),
            'edit' => EditRfqItem::route('/{record}/edit'),
        ];
    }
}
