<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\AuditChecklistItemResource;

class EditAuditChecklistItem extends EditRecord
{
    protected static string $resource = AuditChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
