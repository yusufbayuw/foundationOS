<?php

namespace Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\PreventiveActionResource;

class ViewPreventiveAction extends ViewRecord
{
    protected static string $resource = PreventiveActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
