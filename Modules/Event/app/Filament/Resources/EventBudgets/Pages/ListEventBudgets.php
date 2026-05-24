<?php

namespace Modules\Event\Filament\Resources\EventBudgets\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventBudgets\EventBudgetResource;

class ListEventBudgets extends ListRecords
{
    protected static string $resource = EventBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
