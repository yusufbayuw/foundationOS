<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\SalarySlipComponents\SalarySlipComponentResource;

class ViewSalarySlipComponent extends ViewRecord
{
    protected static string $resource = SalarySlipComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
