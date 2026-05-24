<?php

namespace Modules\Event\Filament\Resources\EventBudgets\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventBudgets\EventBudgetResource;

class ViewEventBudget extends ViewRecord
{
    protected static string $resource = EventBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
