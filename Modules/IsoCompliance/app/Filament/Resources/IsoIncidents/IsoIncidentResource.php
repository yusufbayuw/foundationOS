<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoIncidents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages\CreateIsoIncident;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages\EditIsoIncident;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages\ListIsoIncidents;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages\ViewIsoIncident;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\Schemas\IsoIncidentForm;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\Schemas\IsoIncidentInfolist;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\Tables\IsoIncidentsTable;
use Modules\IsoCompliance\Models\IsoIncident;

class IsoIncidentResource extends ModuleResource
{
    protected static ?string $model = IsoIncident::class;

    public static function form(Schema $schema): Schema
    {
        return IsoIncidentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IsoIncidentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IsoIncidentsTable::configure($table);
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
            'index' => ListIsoIncidents::route('/'),
            'create' => CreateIsoIncident::route('/create'),
            'view' => ViewIsoIncident::route('/{record}'),
            'edit' => EditIsoIncident::route('/{record}/edit'),
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
