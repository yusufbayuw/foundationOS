<?php

namespace Modules\Library\Filament\Resources\BookReservations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\BookReservations\BookReservationResource;

class CreateBookReservation extends CreateRecord
{
    protected static string $resource = BookReservationResource::class;
}
