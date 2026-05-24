<?php

namespace Modules\Alumni\Filament\Resources\AlumniDonations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\AlumniDonations\AlumniDonationResource;

class ListAlumniDonations extends ListRecords
{
    protected static string $resource = AlumniDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
