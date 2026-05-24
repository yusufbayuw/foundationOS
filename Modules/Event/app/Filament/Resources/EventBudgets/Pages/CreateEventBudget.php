<?php

namespace Modules\Event\Filament\Resources\EventBudgets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventBudgets\EventBudgetResource;

class CreateEventBudget extends CreateRecord
{
    protected static string $resource = EventBudgetResource::class;
}
