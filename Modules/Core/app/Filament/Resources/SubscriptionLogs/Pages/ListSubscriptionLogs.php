<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\SubscriptionLogs\SubscriptionLogResource;

class ListSubscriptionLogs extends ListRecords
{
    protected static string $resource = SubscriptionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
