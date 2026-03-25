<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\SalarySlips\SalarySlipResource;

class CreateSalarySlip extends CreateRecord
{
    protected static string $resource = SalarySlipResource::class;
}
