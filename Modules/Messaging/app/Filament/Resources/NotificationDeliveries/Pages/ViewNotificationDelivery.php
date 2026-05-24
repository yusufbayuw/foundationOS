<?php

namespace Modules\Messaging\Filament\Resources\NotificationDeliveries\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\NotificationDeliveryResource;

class ViewNotificationDelivery extends ViewRecord
{
    protected static string $resource = NotificationDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
