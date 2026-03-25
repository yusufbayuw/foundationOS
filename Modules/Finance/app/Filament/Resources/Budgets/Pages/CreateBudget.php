<?php

namespace Modules\Finance\Filament\Resources\Budgets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\Budgets\BudgetResource;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;
}
