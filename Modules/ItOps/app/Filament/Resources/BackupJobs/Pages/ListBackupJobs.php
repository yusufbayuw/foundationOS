<?php

namespace Modules\ItOps\Filament\Resources\BackupJobs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ItOps\Filament\Resources\BackupJobs\BackupJobResource;

class ListBackupJobs extends ListRecords
{
    protected static string $resource = BackupJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
