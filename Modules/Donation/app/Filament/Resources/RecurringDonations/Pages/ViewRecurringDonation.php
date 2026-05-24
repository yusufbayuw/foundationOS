<?php

namespace Modules\Donation\Filament\Resources\RecurringDonations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Donation\Filament\Resources\RecurringDonations\RecurringDonationResource;

class ViewRecurringDonation extends ViewRecord
{
    protected static string $resource = RecurringDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
