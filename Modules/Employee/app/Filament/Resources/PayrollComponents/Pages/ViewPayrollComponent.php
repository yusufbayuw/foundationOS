<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\PayrollComponents\PayrollComponentResource;

class ViewPayrollComponent extends ViewRecord
{
    protected static string $resource = PayrollComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
