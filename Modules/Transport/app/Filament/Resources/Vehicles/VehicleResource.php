<?php

namespace Modules\Transport\Filament\Resources\Vehicles;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\Vehicles\Pages\CreateVehicle;
use Modules\Transport\Filament\Resources\Vehicles\Pages\EditVehicle;
use Modules\Transport\Filament\Resources\Vehicles\Pages\ListVehicles;
use Modules\Transport\Filament\Resources\Vehicles\Pages\ViewVehicle;
use Modules\Transport\Filament\Resources\Vehicles\Schemas\VehicleForm;
use Modules\Transport\Filament\Resources\Vehicles\Tables\VehiclesTable;
use Modules\Transport\Models\Vehicle;

class VehicleResource extends ModuleResource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VehicleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehiclesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicles::route('/'),
            'create' => CreateVehicle::route('/create'),
            'view' => ViewVehicle::route('/{record}'),
            'edit' => EditVehicle::route('/{record}/edit'),
        ];
    }
}
