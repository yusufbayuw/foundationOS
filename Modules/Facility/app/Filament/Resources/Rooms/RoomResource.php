<?php

namespace Modules\Facility\Filament\Resources\Rooms;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\Rooms\Pages\CreateRoom;
use Modules\Facility\Filament\Resources\Rooms\Pages\EditRoom;
use Modules\Facility\Filament\Resources\Rooms\Pages\ListRooms;
use Modules\Facility\Filament\Resources\Rooms\Pages\ViewRoom;
use Modules\Facility\Filament\Resources\Rooms\Schemas\RoomForm;
use Modules\Facility\Filament\Resources\Rooms\Tables\RoomsTable;
use Modules\Facility\Models\Room;

class RoomResource extends ModuleResource
{
    protected static ?string $model = Room::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRooms::route('/'),
            'create' => CreateRoom::route('/create'),
            'view' => ViewRoom::route('/{record}'),
            'edit' => EditRoom::route('/{record}/edit'),
        ];
    }
}
