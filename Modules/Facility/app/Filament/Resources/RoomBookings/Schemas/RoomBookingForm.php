<?php

namespace Modules\Facility\Filament\Resources\RoomBookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoomBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->relationship('organization', 'name'),
                TextInput::make('code'),
                TextInput::make('name'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->columnSpanFull(),
                Select::make('room_id')
                    ->relationship('room', 'name'),
                DateTimePicker::make('start_at'),
                DateTimePicker::make('end_at'),
                TextInput::make('purpose'),
                TextInput::make('requester_user_id')
                    ->numeric(),
                TextInput::make('booking_status')
                    ->required()
                    ->default('draft'),
            ]);
    }
}
