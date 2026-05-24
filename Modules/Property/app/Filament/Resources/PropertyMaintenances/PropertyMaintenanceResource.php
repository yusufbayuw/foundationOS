<?php

namespace Modules\Property\Filament\Resources\PropertyMaintenances;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Property\Filament\Resources\PropertyMaintenances\Pages\CreatePropertyMaintenance;
use Modules\Property\Filament\Resources\PropertyMaintenances\Pages\EditPropertyMaintenance;
use Modules\Property\Filament\Resources\PropertyMaintenances\Pages\ListPropertyMaintenances;
use Modules\Property\Filament\Resources\PropertyMaintenances\Pages\ViewPropertyMaintenance;
use Modules\Property\Filament\Resources\PropertyMaintenances\Schemas\PropertyMaintenanceForm;
use Modules\Property\Filament\Resources\PropertyMaintenances\Schemas\PropertyMaintenanceInfolist;
use Modules\Property\Filament\Resources\PropertyMaintenances\Tables\PropertyMaintenancesTable;
use Modules\Property\Models\PropertyMaintenance;

class PropertyMaintenanceResource extends ModuleResource
{
    protected static ?string $model = PropertyMaintenance::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PropertyMaintenanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PropertyMaintenanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertyMaintenancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPropertyMaintenances::route('/'),
            'create' => CreatePropertyMaintenance::route('/create'),
            'view' => ViewPropertyMaintenance::route('/{record}'),
            'edit' => EditPropertyMaintenance::route('/{record}/edit'),
        ];
    }
}
