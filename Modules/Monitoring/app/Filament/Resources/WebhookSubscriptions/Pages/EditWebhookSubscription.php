<?php

namespace Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\WebhookSubscriptionResource;

class EditWebhookSubscription extends EditRecord
{
    protected static string $resource = WebhookSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
