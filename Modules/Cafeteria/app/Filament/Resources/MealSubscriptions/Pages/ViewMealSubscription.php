<?php

namespace Modules\Cafeteria\Filament\Resources\MealSubscriptions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\MealSubscriptionResource;

class ViewMealSubscription extends ViewRecord
{
    protected static string $resource = MealSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
