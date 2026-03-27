<?php

namespace Modules\Library\Filament\Resources\BookReservations\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Library\Filament\Resources\BookReservations\BookReservationResource;

class EditBookReservation extends EditRecord
{
    protected static string $resource = BookReservationResource::class;
}
