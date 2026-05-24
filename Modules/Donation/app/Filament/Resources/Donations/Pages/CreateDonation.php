<?php

namespace Modules\Donation\Filament\Resources\Donations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Donation\Filament\Resources\Donations\DonationResource;

class CreateDonation extends CreateRecord
{
    protected static string $resource = DonationResource::class;
}
