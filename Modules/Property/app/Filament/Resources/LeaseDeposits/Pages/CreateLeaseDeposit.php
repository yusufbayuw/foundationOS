<?php

namespace Modules\Property\Filament\Resources\LeaseDeposits\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Property\Filament\Resources\LeaseDeposits\LeaseDepositResource;

class CreateLeaseDeposit extends CreateRecord
{
    protected static string $resource = LeaseDepositResource::class;
}
