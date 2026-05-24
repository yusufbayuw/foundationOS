<?php

namespace Modules\Donation\Filament\Resources\Donors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Donation\Filament\Resources\Donors\DonorResource;

class CreateDonor extends CreateRecord
{
    protected static string $resource = DonorResource::class;
}
