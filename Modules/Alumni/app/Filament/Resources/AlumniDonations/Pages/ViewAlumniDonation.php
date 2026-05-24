<?php

namespace Modules\Alumni\Filament\Resources\AlumniDonations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\AlumniDonations\AlumniDonationResource;

class ViewAlumniDonation extends ViewRecord
{
    protected static string $resource = AlumniDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
