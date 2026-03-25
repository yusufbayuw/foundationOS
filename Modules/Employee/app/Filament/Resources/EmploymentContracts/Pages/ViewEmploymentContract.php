<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\EmploymentContracts\EmploymentContractResource;

class ViewEmploymentContract extends ViewRecord
{
    protected static string $resource = EmploymentContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
