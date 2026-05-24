<?php

namespace Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\WebhookDeliveryResource;

class ListWebhookDeliveries extends ListRecords
{
    protected static string $resource = WebhookDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
