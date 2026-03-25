<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\ProcurementItems\Pages\CreateProcurementItem;
use Modules\Procurement\Filament\Resources\ProcurementItems\Pages\EditProcurementItem;
use Modules\Procurement\Filament\Resources\ProcurementItems\Pages\ListProcurementItems;
use Modules\Procurement\Filament\Resources\ProcurementItems\Pages\ViewProcurementItem;
use Modules\Procurement\Filament\Resources\ProcurementItems\Schemas\ProcurementItemForm;
use Modules\Procurement\Filament\Resources\ProcurementItems\Schemas\ProcurementItemInfolist;
use Modules\Procurement\Filament\Resources\ProcurementItems\Tables\ProcurementItemsTable;
use Modules\Procurement\Models\ProcurementItem;

class ProcurementItemResource extends LocalizedResource
{
    protected static ?string $model = ProcurementItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProcurementItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProcurementItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcurementItemsTable::configure($table);
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
            'index' => ListProcurementItems::route('/'),
            'create' => CreateProcurementItem::route('/create'),
            'view' => ViewProcurementItem::route('/{record}'),
            'edit' => EditProcurementItem::route('/{record}/edit'),
        ];
    }
}
