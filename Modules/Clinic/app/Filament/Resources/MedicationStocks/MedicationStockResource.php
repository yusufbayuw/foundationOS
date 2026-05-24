<?php

namespace Modules\Clinic\Filament\Resources\MedicationStocks;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\MedicationStocks\Pages\CreateMedicationStock;
use Modules\Clinic\Filament\Resources\MedicationStocks\Pages\EditMedicationStock;
use Modules\Clinic\Filament\Resources\MedicationStocks\Pages\ListMedicationStocks;
use Modules\Clinic\Filament\Resources\MedicationStocks\Pages\ViewMedicationStock;
use Modules\Clinic\Filament\Resources\MedicationStocks\Schemas\MedicationStockForm;
use Modules\Clinic\Filament\Resources\MedicationStocks\Schemas\MedicationStockInfolist;
use Modules\Clinic\Filament\Resources\MedicationStocks\Tables\MedicationStocksTable;
use Modules\Clinic\Models\MedicationStock;
use Modules\Core\Filament\Support\ModuleResource;

class MedicationStockResource extends ModuleResource
{
    protected static ?string $model = MedicationStock::class;

    public static function form(Schema $schema): Schema
    {
        return MedicationStockForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MedicationStockInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicationStocksTable::configure($table);
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
            'index' => ListMedicationStocks::route('/'),
            'create' => CreateMedicationStock::route('/create'),
            'view' => ViewMedicationStock::route('/{record}'),
            'edit' => EditMedicationStock::route('/{record}/edit'),
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
