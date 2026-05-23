<?php

namespace App\Filament\Parent\Resources\ChildAnnouncements\Pages;

use App\Filament\Parent\Resources\ChildAnnouncements\ChildAnnouncementResource;
use Filament\Resources\Pages\ListRecords;

class ListChildAnnouncements extends ListRecords
{
    protected static string $resource = ChildAnnouncementResource::class;
}
