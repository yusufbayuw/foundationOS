<?php

namespace Modules\Alumni\Filament\Resources\Alumni\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\Alumni\AlumnusResource;

class ListAlumni extends ListRecords
{
    protected static string $resource = AlumnusResource::class;
}
