<?php

namespace Modules\Donation\Filament\Resources\RecurringDonations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Donation\Filament\Resources\RecurringDonations\RecurringDonationResource;

class CreateRecurringDonation extends CreateRecord
{
    protected static string $resource = RecurringDonationResource::class;
}
