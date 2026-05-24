<?php

namespace Modules\Facility\Filament\Resources\RoomMaintenanceLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Pages\CreateRoomMaintenanceLog;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Pages\EditRoomMaintenanceLog;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Pages\ListRoomMaintenanceLogs;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Pages\ViewRoomMaintenanceLog;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Schemas\RoomMaintenanceLogForm;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Schemas\RoomMaintenanceLogInfolist;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Tables\RoomMaintenanceLogsTable;
use Modules\Facility\Models\RoomMaintenanceLog;

class RoomMaintenanceLogResource extends ModuleResource
{
    protected static ?string $model = RoomMaintenanceLog::class;

    public static function form(Schema $schema): Schema
    {
        return RoomMaintenanceLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomMaintenanceLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomMaintenanceLogsTable::configure($table);
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
            'index' => ListRoomMaintenanceLogs::route('/'),
            'create' => CreateRoomMaintenanceLog::route('/create'),
            'view' => ViewRoomMaintenanceLog::route('/{record}'),
            'edit' => EditRoomMaintenanceLog::route('/{record}/edit'),
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
