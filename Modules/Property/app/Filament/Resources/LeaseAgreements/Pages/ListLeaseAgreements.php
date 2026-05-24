<?php

namespace Modules\Property\Filament\Resources\LeaseAgreements\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Property\Filament\Resources\LeaseAgreements\LeaseAgreementResource;

class ListLeaseAgreements extends ListRecords
{
    protected static string $resource = LeaseAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
