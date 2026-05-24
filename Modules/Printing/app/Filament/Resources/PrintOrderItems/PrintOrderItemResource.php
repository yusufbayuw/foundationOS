<?php

namespace Modules\Printing\Filament\Resources\PrintOrderItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\PrintOrderItems\Pages\CreatePrintOrderItem;
use Modules\Printing\Filament\Resources\PrintOrderItems\Pages\EditPrintOrderItem;
use Modules\Printing\Filament\Resources\PrintOrderItems\Pages\ListPrintOrderItems;
use Modules\Printing\Filament\Resources\PrintOrderItems\Pages\ViewPrintOrderItem;
use Modules\Printing\Filament\Resources\PrintOrderItems\Schemas\PrintOrderItemForm;
use Modules\Printing\Filament\Resources\PrintOrderItems\Schemas\PrintOrderItemInfolist;
use Modules\Printing\Filament\Resources\PrintOrderItems\Tables\PrintOrderItemsTable;
use Modules\Printing\Models\PrintOrderItem;

class PrintOrderItemResource extends ModuleResource
{
    protected static ?string $model = PrintOrderItem::class;

    public static function form(Schema $schema): Schema
    {
        return PrintOrderItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrintOrderItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintOrderItemsTable::configure($table);
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
            'index' => ListPrintOrderItems::route('/'),
            'create' => CreatePrintOrderItem::route('/create'),
            'view' => ViewPrintOrderItem::route('/{record}'),
            'edit' => EditPrintOrderItem::route('/{record}/edit'),
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
