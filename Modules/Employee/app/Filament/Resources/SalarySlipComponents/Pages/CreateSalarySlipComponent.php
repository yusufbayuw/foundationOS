<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\SalarySlipComponents\SalarySlipComponentResource;

class CreateSalarySlipComponent extends CreateRecord
{
    protected static string $resource = SalarySlipComponentResource::class;
}
