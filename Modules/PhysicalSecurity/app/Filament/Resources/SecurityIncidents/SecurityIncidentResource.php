<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Pages\CreateSecurityIncident;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Pages\EditSecurityIncident;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Pages\ListSecurityIncidents;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Pages\ViewSecurityIncident;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Schemas\SecurityIncidentForm;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Schemas\SecurityIncidentInfolist;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Tables\SecurityIncidentsTable;
use Modules\PhysicalSecurity\Models\SecurityIncident;

class SecurityIncidentResource extends ModuleResource
{
    protected static ?string $model = SecurityIncident::class;

    public static function form(Schema $schema): Schema
    {
        return SecurityIncidentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SecurityIncidentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SecurityIncidentsTable::configure($table);
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
            'index' => ListSecurityIncidents::route('/'),
            'create' => CreateSecurityIncident::route('/create'),
            'view' => ViewSecurityIncident::route('/{record}'),
            'edit' => EditSecurityIncident::route('/{record}/edit'),
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
