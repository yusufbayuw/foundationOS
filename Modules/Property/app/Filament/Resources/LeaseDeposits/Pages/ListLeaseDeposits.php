<?php

namespace Modules\Property\Filament\Resources\LeaseDeposits\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Property\Filament\Resources\LeaseDeposits\LeaseDepositResource;

class ListLeaseDeposits extends ListRecords
{
    protected static string $resource = LeaseDepositResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
