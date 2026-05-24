<?php

namespace Modules\Risk\Filament\Resources\RiskIncidents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Risk\Filament\Resources\RiskIncidents\Pages\CreateRiskIncident;
use Modules\Risk\Filament\Resources\RiskIncidents\Pages\EditRiskIncident;
use Modules\Risk\Filament\Resources\RiskIncidents\Pages\ListRiskIncidents;
use Modules\Risk\Filament\Resources\RiskIncidents\Pages\ViewRiskIncident;
use Modules\Risk\Filament\Resources\RiskIncidents\Schemas\RiskIncidentForm;
use Modules\Risk\Filament\Resources\RiskIncidents\Schemas\RiskIncidentInfolist;
use Modules\Risk\Filament\Resources\RiskIncidents\Tables\RiskIncidentsTable;
use Modules\Risk\Models\RiskIncident;

class RiskIncidentResource extends ModuleResource
{
    protected static ?string $model = RiskIncident::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RiskIncidentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RiskIncidentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskIncidentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRiskIncidents::route('/'),
            'create' => CreateRiskIncident::route('/create'),
            'view' => ViewRiskIncident::route('/{record}'),
            'edit' => EditRiskIncident::route('/{record}/edit'),
        ];
    }
}
