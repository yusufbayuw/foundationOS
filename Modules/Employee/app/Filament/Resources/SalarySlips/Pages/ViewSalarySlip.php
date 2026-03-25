<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\SalarySlips\SalarySlipResource;

class ViewSalarySlip extends ViewRecord
{
    protected static string $resource = SalarySlipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
