<?php

namespace Modules\Property\Filament\Resources\LeaseDeposits\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Property\Filament\Resources\LeaseDeposits\LeaseDepositResource;

class ViewLeaseDeposit extends ViewRecord
{
    protected static string $resource = LeaseDepositResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
