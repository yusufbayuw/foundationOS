<?php

namespace Modules\EOffice\Filament\Resources\LetterAttachments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EOffice\Filament\Resources\LetterAttachments\LetterAttachmentResource;

class ViewLetterAttachment extends ViewRecord
{
    protected static string $resource = LetterAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
