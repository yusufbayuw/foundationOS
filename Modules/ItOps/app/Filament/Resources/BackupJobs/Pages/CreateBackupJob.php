<?php

namespace Modules\ItOps\Filament\Resources\BackupJobs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ItOps\Filament\Resources\BackupJobs\BackupJobResource;

class CreateBackupJob extends CreateRecord
{
    protected static string $resource = BackupJobResource::class;
}
