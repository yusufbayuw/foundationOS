<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Monitoring\Filament\Resources\FileUploads\FileUploadResource;

class ViewFileUpload extends ViewRecord
{
    protected static string $resource = FileUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
