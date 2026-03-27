<?php

namespace Modules\Library\Filament\Resources\BookReservations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\BookReservations\Pages\CreateBookReservation;
use Modules\Library\Filament\Resources\BookReservations\Pages\EditBookReservation;
use Modules\Library\Filament\Resources\BookReservations\Pages\ListBookReservations;
use Modules\Library\Filament\Resources\BookReservations\Pages\ViewBookReservation;
use Modules\Library\Filament\Resources\BookReservations\Schemas\BookReservationForm;
use Modules\Library\Filament\Resources\BookReservations\Schemas\BookReservationInfolist;
use Modules\Library\Filament\Resources\BookReservations\Tables\BookReservationsTable;
use Modules\Library\Filament\Resources\LibraryResource;
use Modules\Library\Models\BookReservation;

class BookReservationResource extends LibraryResource
{
    protected static ?string $model = BookReservation::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return BookReservationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookReservationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookReservationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookReservations::route('/'),
            'create' => CreateBookReservation::route('/create'),
            'view' => ViewBookReservation::route('/{record}'),
            'edit' => EditBookReservation::route('/{record}/edit'),
        ];
    }
}
