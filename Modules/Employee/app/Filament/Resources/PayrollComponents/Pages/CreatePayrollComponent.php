<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\PayrollComponents\PayrollComponentResource;

class CreatePayrollComponent extends CreateRecord
{
    protected static string $resource = PayrollComponentResource::class;
}
