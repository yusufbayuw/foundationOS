<?php

namespace Modules\Printing\Filament\Resources\PublicationAuthors\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Printing\Filament\Resources\PublicationAuthors\PublicationAuthorResource;

class ListPublicationAuthors extends ListRecords
{
    protected static string $resource = PublicationAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
