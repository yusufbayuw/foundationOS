<?php

namespace Modules\Transport\Filament\Resources\VehicleOperatingCosts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\Pages\CreateVehicleOperatingCost;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\Pages\EditVehicleOperatingCost;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\Pages\ListVehicleOperatingCosts;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\Pages\ViewVehicleOperatingCost;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\Schemas\VehicleOperatingCostForm;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\Schemas\VehicleOperatingCostInfolist;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\Tables\VehicleOperatingCostsTable;
use Modules\Transport\Models\VehicleOperatingCost;

class VehicleOperatingCostResource extends ModuleResource
{
    protected static ?string $model = VehicleOperatingCost::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VehicleOperatingCostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VehicleOperatingCostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleOperatingCostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleOperatingCosts::route('/'),
            'create' => CreateVehicleOperatingCost::route('/create'),
            'view' => ViewVehicleOperatingCost::route('/{record}'),
            'edit' => EditVehicleOperatingCost::route('/{record}/edit'),
        ];
    }
}
