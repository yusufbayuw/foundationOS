<?php

namespace Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Pages\CreateEmergencyAlert;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Pages\EditEmergencyAlert;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Pages\ListEmergencyAlerts;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Pages\ViewEmergencyAlert;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Schemas\EmergencyAlertForm;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Schemas\EmergencyAlertInfolist;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Tables\EmergencyAlertsTable;
use Modules\PhysicalSecurity\Models\EmergencyAlert;

class EmergencyAlertResource extends ModuleResource
{
    protected static ?string $model = EmergencyAlert::class;

    public static function form(Schema $schema): Schema
    {
        return EmergencyAlertForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmergencyAlertInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmergencyAlertsTable::configure($table);
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
            'index' => ListEmergencyAlerts::route('/'),
            'create' => CreateEmergencyAlert::route('/create'),
            'view' => ViewEmergencyAlert::route('/{record}'),
            'edit' => EditEmergencyAlert::route('/{record}/edit'),
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
