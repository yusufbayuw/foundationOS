<?php

namespace Modules\Library\Filament\Resources\BookReservations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\BookReservations\BookReservationResource;

class ListBookReservations extends ListRecords
{
    protected static string $resource = BookReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
