<?php

namespace Modules\Messaging\Filament\Resources\NotificationTemplates\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Messaging\Filament\Resources\NotificationTemplates\NotificationTemplateResource;

class ListNotificationTemplates extends ListRecords
{
    protected static string $resource = NotificationTemplateResource::class;
}
