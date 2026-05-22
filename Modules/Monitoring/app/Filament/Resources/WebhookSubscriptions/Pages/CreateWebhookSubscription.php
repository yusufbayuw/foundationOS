<?php

namespace Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\WebhookSubscriptionResource;

class CreateWebhookSubscription extends CreateRecord
{
    protected static string $resource = WebhookSubscriptionResource::class;
}
