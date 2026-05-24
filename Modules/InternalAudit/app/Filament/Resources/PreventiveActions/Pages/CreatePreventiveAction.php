<?php

namespace Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\PreventiveActionResource;

class CreatePreventiveAction extends CreateRecord
{
    protected static string $resource = PreventiveActionResource::class;
}
