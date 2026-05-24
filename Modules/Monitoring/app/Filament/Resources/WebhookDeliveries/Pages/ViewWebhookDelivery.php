<?php

namespace Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\WebhookDeliveryResource;

class ViewWebhookDelivery extends ViewRecord
{
    protected static string $resource = WebhookDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
