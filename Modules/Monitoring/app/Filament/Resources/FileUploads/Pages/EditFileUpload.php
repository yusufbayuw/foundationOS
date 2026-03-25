<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Monitoring\Filament\Resources\FileUploads\FileUploadResource;

class EditFileUpload extends EditRecord
{
    protected static string $resource = FileUploadResource::class;

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
