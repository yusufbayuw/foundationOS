<?php

namespace Modules\ItOps\Filament\Resources\BackupJobs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ItOps\Filament\Resources\BackupJobs\BackupJobResource;

class EditBackupJob extends EditRecord
{
    protected static string $resource = BackupJobResource::class;

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
