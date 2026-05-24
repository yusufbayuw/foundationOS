<?php

namespace Modules\Facility\Filament\Resources\RoomBookings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\RoomBookings\Pages\CreateRoomBooking;
use Modules\Facility\Filament\Resources\RoomBookings\Pages\EditRoomBooking;
use Modules\Facility\Filament\Resources\RoomBookings\Pages\ListRoomBookings;
use Modules\Facility\Filament\Resources\RoomBookings\Pages\ViewRoomBooking;
use Modules\Facility\Filament\Resources\RoomBookings\Schemas\RoomBookingForm;
use Modules\Facility\Filament\Resources\RoomBookings\Schemas\RoomBookingInfolist;
use Modules\Facility\Filament\Resources\RoomBookings\Tables\RoomBookingsTable;
use Modules\Facility\Models\RoomBooking;

class RoomBookingResource extends ModuleResource
{
    protected static ?string $model = RoomBooking::class;

    public static function form(Schema $schema): Schema
    {
        return RoomBookingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomBookingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomBookingsTable::configure($table);
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
            'index' => ListRoomBookings::route('/'),
            'create' => CreateRoomBooking::route('/create'),
            'view' => ViewRoomBooking::route('/{record}'),
            'edit' => EditRoomBooking::route('/{record}/edit'),
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
