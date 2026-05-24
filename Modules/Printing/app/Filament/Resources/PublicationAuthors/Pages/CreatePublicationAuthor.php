<?php

namespace Modules\Printing\Filament\Resources\PublicationAuthors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\PublicationAuthors\PublicationAuthorResource;

class CreatePublicationAuthor extends CreateRecord
{
    protected static string $resource = PublicationAuthorResource::class;
}
