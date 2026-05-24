<?php

namespace Modules\Messaging\Filament\Resources\NotificationPreferences\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Messaging\Filament\Resources\NotificationPreferences\NotificationPreferenceResource;

class ViewNotificationPreference extends ViewRecord
{
    protected static string $resource = NotificationPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
