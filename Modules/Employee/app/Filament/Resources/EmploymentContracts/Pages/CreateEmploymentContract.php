<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\EmploymentContracts\EmploymentContractResource;

class CreateEmploymentContract extends CreateRecord
{
    protected static string $resource = EmploymentContractResource::class;
}
