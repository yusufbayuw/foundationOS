<?php

namespace App\Filament\Imports;

use Modules\Monitoring\Models\FileUpload;

class FileUploadImporter extends BaseModelImporter
{
    protected static ?string $model = FileUpload::class;
}
