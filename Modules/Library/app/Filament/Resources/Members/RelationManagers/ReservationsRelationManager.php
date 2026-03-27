<?php

namespace Modules\Library\Filament\Resources\Members\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\BookReservations\Schemas\BookReservationForm;
use Modules\Library\Filament\Resources\BookReservations\Tables\BookReservationsTable;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';

    public function form(Schema $schema): Schema
    {
        return BookReservationForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return BookReservationsTable::configure($table);
    }
}
