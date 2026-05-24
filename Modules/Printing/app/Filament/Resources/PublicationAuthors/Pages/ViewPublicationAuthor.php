<?php

namespace Modules\Printing\Filament\Resources\PublicationAuthors\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Printing\Filament\Resources\PublicationAuthors\PublicationAuthorResource;

class ViewPublicationAuthor extends ViewRecord
{
    protected static string $resource = PublicationAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
