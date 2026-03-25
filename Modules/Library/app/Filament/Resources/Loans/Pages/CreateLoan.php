<?php

namespace Modules\Library\Filament\Resources\Loans\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\Loans\LoanResource;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;
}
