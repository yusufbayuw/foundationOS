<?php

namespace Modules\Event\Filament\Resources\EventCommittees\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventCommittees\EventCommitteeResource;

class CreateEventCommittee extends CreateRecord
{
    protected static string $resource = EventCommitteeResource::class;
}
