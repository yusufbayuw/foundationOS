<?php

namespace Modules\Messaging\Filament\Resources\NotificationDeliveries\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\NotificationDeliveryResource;

class ListNotificationDeliveries extends ListRecords
{
    protected static string $resource = NotificationDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
