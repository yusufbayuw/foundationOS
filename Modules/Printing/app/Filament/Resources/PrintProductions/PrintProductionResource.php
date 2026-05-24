<?php

namespace Modules\Printing\Filament\Resources\PrintProductions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\PrintProductions\Pages\CreatePrintProduction;
use Modules\Printing\Filament\Resources\PrintProductions\Pages\EditPrintProduction;
use Modules\Printing\Filament\Resources\PrintProductions\Pages\ListPrintProductions;
use Modules\Printing\Filament\Resources\PrintProductions\Pages\ViewPrintProduction;
use Modules\Printing\Filament\Resources\PrintProductions\Schemas\PrintProductionForm;
use Modules\Printing\Filament\Resources\PrintProductions\Schemas\PrintProductionInfolist;
use Modules\Printing\Filament\Resources\PrintProductions\Tables\PrintProductionsTable;
use Modules\Printing\Models\PrintProduction;

class PrintProductionResource extends ModuleResource
{
    protected static ?string $model = PrintProduction::class;

    public static function form(Schema $schema): Schema
    {
        return PrintProductionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrintProductionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintProductionsTable::configure($table);
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
            'index' => ListPrintProductions::route('/'),
            'create' => CreatePrintProduction::route('/create'),
            'view' => ViewPrintProduction::route('/{record}'),
            'edit' => EditPrintProduction::route('/{record}/edit'),
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
