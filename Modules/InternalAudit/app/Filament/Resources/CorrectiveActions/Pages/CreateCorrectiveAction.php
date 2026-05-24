<?php

namespace Modules\InternalAudit\Filament\Resources\CorrectiveActions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\CorrectiveActionResource;

class CreateCorrectiveAction extends CreateRecord
{
    protected static string $resource = CorrectiveActionResource::class;
}
