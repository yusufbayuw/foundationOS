<?php

namespace Modules\Messaging\Filament\Resources\NotificationDeliveries\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\NotificationDeliveryResource;

class CreateNotificationDelivery extends CreateRecord
{
    protected static string $resource = NotificationDeliveryResource::class;
}
