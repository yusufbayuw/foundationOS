<?php

namespace Modules\EOffice\Filament\Resources\Letters\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\EOffice\Filament\Resources\Letters\LetterResource;

class CreateLetter extends CreateRecord
{
    protected static string $resource = LetterResource::class;
}
