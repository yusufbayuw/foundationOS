<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\SubscriptionPlans\SubscriptionPlanResource;

class CreateSubscriptionPlan extends CreateRecord
{
    protected static string $resource = SubscriptionPlanResource::class;
}
