<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Employee\Filament\Resources\PayrollComponents\PayrollComponentResource;

class ListPayrollComponents extends ListRecords
{
    protected static string $resource = PayrollComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
