<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\SubscriptionLogs\SubscriptionLogResource;

class CreateSubscriptionLog extends CreateRecord
{
    protected static string $resource = SubscriptionLogResource::class;
}
