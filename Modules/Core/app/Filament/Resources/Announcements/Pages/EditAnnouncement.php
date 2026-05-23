<?php

namespace Modules\Core\Filament\Resources\Announcements\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\Announcements\AnnouncementResource;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;
}
