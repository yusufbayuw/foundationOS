<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Monitoring\Filament\Resources\FileUploads\FileUploadResource;

class CreateFileUpload extends CreateRecord
{
    protected static string $resource = FileUploadResource::class;
}
