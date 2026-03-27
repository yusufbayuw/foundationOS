<?php

namespace Modules\Library\Filament\Resources\Books\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\BookReservations\BookReservationResource;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';

    public function form(Schema $schema): Schema
    {
        return BookReservationResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return BookReservationResource::table($table);
    }
}
