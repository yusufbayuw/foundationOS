<?php

namespace Modules\Core\Filament\Resources\Announcements\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\Announcements\AnnouncementResource;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;
}
