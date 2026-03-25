<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Employee\Filament\Resources\EmploymentContracts\EmploymentContractResource;

class ListEmploymentContracts extends ListRecords
{
    protected static string $resource = EmploymentContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
