<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\SubscriptionLogs\SubscriptionLogResource;

class ViewSubscriptionLog extends ViewRecord
{
    protected static string $resource = SubscriptionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
