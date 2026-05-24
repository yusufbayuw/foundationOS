<?php

namespace Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\WebhookDeliveryResource;

class EditWebhookDelivery extends EditRecord
{
    protected static string $resource = WebhookDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
