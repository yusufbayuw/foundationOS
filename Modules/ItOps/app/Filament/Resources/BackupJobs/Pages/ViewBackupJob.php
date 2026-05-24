<?php

namespace Modules\ItOps\Filament\Resources\BackupJobs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ItOps\Filament\Resources\BackupJobs\BackupJobResource;

class ViewBackupJob extends ViewRecord
{
    protected static string $resource = BackupJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
