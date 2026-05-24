<?php

namespace Modules\Messaging\Filament\Resources\NotificationPreferences\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Messaging\Filament\Resources\NotificationPreferences\NotificationPreferenceResource;

class ListNotificationPreferences extends ListRecords
{
    protected static string $resource = NotificationPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
