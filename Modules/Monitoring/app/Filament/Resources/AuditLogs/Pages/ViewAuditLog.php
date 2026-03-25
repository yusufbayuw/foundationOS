<?php

namespace Modules\Monitoring\Filament\Resources\AuditLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Monitoring\Filament\Resources\AuditLogs\AuditLogResource;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
