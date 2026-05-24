<?php

namespace Modules\Donation\Filament\Resources\RecurringDonations\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Donation\Filament\Resources\RecurringDonations\RecurringDonationResource;

class EditRecurringDonation extends EditRecord
{
    protected static string $resource = RecurringDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
