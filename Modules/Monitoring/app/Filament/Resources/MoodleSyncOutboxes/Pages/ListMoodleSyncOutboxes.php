<?php

namespace Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\MoodleSyncOutboxResource;

class ListMoodleSyncOutboxes extends ListRecords
{
    protected static string $resource = MoodleSyncOutboxResource::class;
}
