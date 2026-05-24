<?php

namespace Modules\Facility\Filament\Resources\Buildings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\Buildings\Pages\CreateBuilding;
use Modules\Facility\Filament\Resources\Buildings\Pages\EditBuilding;
use Modules\Facility\Filament\Resources\Buildings\Pages\ListBuildings;
use Modules\Facility\Filament\Resources\Buildings\Pages\ViewBuilding;
use Modules\Facility\Filament\Resources\Buildings\Schemas\BuildingForm;
use Modules\Facility\Filament\Resources\Buildings\Schemas\BuildingInfolist;
use Modules\Facility\Filament\Resources\Buildings\Tables\BuildingsTable;
use Modules\Facility\Models\Building;

class BuildingResource extends ModuleResource
{
    protected static ?string $model = Building::class;

    public static function form(Schema $schema): Schema
    {
        return BuildingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BuildingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuildingsTable::configure($table);
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
            'index' => ListBuildings::route('/'),
            'create' => CreateBuilding::route('/create'),
            'view' => ViewBuilding::route('/{record}'),
            'edit' => EditBuilding::route('/{record}/edit'),
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
