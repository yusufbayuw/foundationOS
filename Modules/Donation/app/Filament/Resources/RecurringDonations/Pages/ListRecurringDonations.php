<?php

namespace Modules\Donation\Filament\Resources\RecurringDonations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Donation\Filament\Resources\RecurringDonations\RecurringDonationResource;

class ListRecurringDonations extends ListRecords
{
    protected static string $resource = RecurringDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
