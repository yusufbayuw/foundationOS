<?php

namespace Modules\EOffice\Filament\Resources\Letters\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\EOffice\Filament\Resources\Letters\LetterResource;

class ListLetters extends ListRecords
{
    protected static string $resource = LetterResource::class;
}
