<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Monitoring\Filament\Resources\FileUploads\FileUploadResource;

class ListFileUploads extends ListRecords
{
    protected static string $resource = FileUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
