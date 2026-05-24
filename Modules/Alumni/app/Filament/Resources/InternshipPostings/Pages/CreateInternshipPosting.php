<?php

namespace Modules\Alumni\Filament\Resources\InternshipPostings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Alumni\Filament\Resources\InternshipPostings\InternshipPostingResource;

class CreateInternshipPosting extends CreateRecord
{
    protected static string $resource = InternshipPostingResource::class;
}
