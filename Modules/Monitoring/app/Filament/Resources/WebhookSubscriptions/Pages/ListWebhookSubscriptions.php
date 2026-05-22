<?php

namespace Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\WebhookSubscriptionResource;

class ListWebhookSubscriptions extends ListRecords
{
    protected static string $resource = WebhookSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
