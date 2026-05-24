<?php

namespace Modules\InternalAudit\Filament\Resources\CorrectiveActions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\CorrectiveActionResource;

class ViewCorrectiveAction extends ViewRecord
{
    protected static string $resource = CorrectiveActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
