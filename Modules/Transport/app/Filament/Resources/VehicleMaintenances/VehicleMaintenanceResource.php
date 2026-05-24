<?php

namespace Modules\Transport\Filament\Resources\VehicleMaintenances;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\VehicleMaintenances\Pages\CreateVehicleMaintenance;
use Modules\Transport\Filament\Resources\VehicleMaintenances\Pages\EditVehicleMaintenance;
use Modules\Transport\Filament\Resources\VehicleMaintenances\Pages\ListVehicleMaintenances;
use Modules\Transport\Filament\Resources\VehicleMaintenances\Pages\ViewVehicleMaintenance;
use Modules\Transport\Filament\Resources\VehicleMaintenances\Schemas\VehicleMaintenanceForm;
use Modules\Transport\Filament\Resources\VehicleMaintenances\Schemas\VehicleMaintenanceInfolist;
use Modules\Transport\Filament\Resources\VehicleMaintenances\Tables\VehicleMaintenancesTable;
use Modules\Transport\Models\VehicleMaintenance;

class VehicleMaintenanceResource extends ModuleResource
{
    protected static ?string $model = VehicleMaintenance::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VehicleMaintenanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VehicleMaintenanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleMaintenancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleMaintenances::route('/'),
            'create' => CreateVehicleMaintenance::route('/create'),
            'view' => ViewVehicleMaintenance::route('/{record}'),
            'edit' => EditVehicleMaintenance::route('/{record}/edit'),
        ];
    }
}
