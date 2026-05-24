<?php

namespace Modules\Cafeteria\Filament\Resources\MealSubscriptions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\MealSubscriptionResource;

class ListMealSubscriptions extends ListRecords
{
    protected static string $resource = MealSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
