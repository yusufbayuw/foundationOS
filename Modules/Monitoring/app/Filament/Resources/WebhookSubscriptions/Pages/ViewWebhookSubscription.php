<?php

namespace Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\WebhookSubscriptionResource;

class ViewWebhookSubscription extends ViewRecord
{
    protected static string $resource = WebhookSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
