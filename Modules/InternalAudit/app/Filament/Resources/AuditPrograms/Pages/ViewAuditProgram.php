<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPrograms\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\AuditProgramResource;

class ViewAuditProgram extends ViewRecord
{
    protected static string $resource = AuditProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
