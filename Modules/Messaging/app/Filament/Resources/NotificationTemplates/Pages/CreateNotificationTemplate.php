<?php

namespace Modules\Messaging\Filament\Resources\NotificationTemplates\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Messaging\Filament\Resources\NotificationTemplates\NotificationTemplateResource;

class CreateNotificationTemplate extends CreateRecord
{
    protected static string $resource = NotificationTemplateResource::class;
}
