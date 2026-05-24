<?php

namespace Modules\EOffice\Filament\Resources\LetterAttachments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EOffice\Filament\Resources\LetterAttachments\LetterAttachmentResource;

class ListLetterAttachments extends ListRecords
{
    protected static string $resource = LetterAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
