<?php

namespace Modules\Printing\Filament\Resources\PrintOrders;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\PrintOrders\Pages\CreatePrintOrder;
use Modules\Printing\Filament\Resources\PrintOrders\Pages\EditPrintOrder;
use Modules\Printing\Filament\Resources\PrintOrders\Pages\ListPrintOrders;
use Modules\Printing\Filament\Resources\PrintOrders\Pages\ViewPrintOrder;
use Modules\Printing\Filament\Resources\PrintOrders\Schemas\PrintOrderForm;
use Modules\Printing\Filament\Resources\PrintOrders\Schemas\PrintOrderInfolist;
use Modules\Printing\Filament\Resources\PrintOrders\Tables\PrintOrdersTable;
use Modules\Printing\Models\PrintOrder;

class PrintOrderResource extends ModuleResource
{
    protected static ?string $model = PrintOrder::class;

    public static function form(Schema $schema): Schema
    {
        return PrintOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrintOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintOrdersTable::configure($table);
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
            'index' => ListPrintOrders::route('/'),
            'create' => CreatePrintOrder::route('/create'),
            'view' => ViewPrintOrder::route('/{record}'),
            'edit' => EditPrintOrder::route('/{record}/edit'),
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
