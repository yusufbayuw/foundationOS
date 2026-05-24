<?php

namespace Modules\Core\Filament\Resources\Polls\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\Polls\PollResource;

class CreatePoll extends CreateRecord
{
    protected static string $resource = PollResource::class;
}
