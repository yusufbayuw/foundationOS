<?php

namespace Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\WebhookDeliveryResource;

class CreateWebhookDelivery extends CreateRecord
{
    protected static string $resource = WebhookDeliveryResource::class;
}
