<?php

namespace Modules\Cafeteria\Filament\Resources\MealSubscriptions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\MealSubscriptionResource;

class CreateMealSubscription extends CreateRecord
{
    protected static string $resource = MealSubscriptionResource::class;
}
