<?php

namespace Modules\Facility\Filament\Resources\RoomEquipment;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\RoomEquipment\Pages\CreateRoomEquipment;
use Modules\Facility\Filament\Resources\RoomEquipment\Pages\EditRoomEquipment;
use Modules\Facility\Filament\Resources\RoomEquipment\Pages\ListRoomEquipment;
use Modules\Facility\Filament\Resources\RoomEquipment\Pages\ViewRoomEquipment;
use Modules\Facility\Filament\Resources\RoomEquipment\Schemas\RoomEquipmentForm;
use Modules\Facility\Filament\Resources\RoomEquipment\Schemas\RoomEquipmentInfolist;
use Modules\Facility\Filament\Resources\RoomEquipment\Tables\RoomEquipmentTable;
use Modules\Facility\Models\RoomEquipment;

class RoomEquipmentResource extends ModuleResource
{
    protected static ?string $model = RoomEquipment::class;

    public static function form(Schema $schema): Schema
    {
        return RoomEquipmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomEquipmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomEquipmentTable::configure($table);
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
            'index' => ListRoomEquipment::route('/'),
            'create' => CreateRoomEquipment::route('/create'),
            'view' => ViewRoomEquipment::route('/{record}'),
            'edit' => EditRoomEquipment::route('/{record}/edit'),
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
