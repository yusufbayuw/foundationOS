<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\TuitionTypes\TuitionTypeResource;

class CreateTuitionType extends CreateRecord
{
    protected static string $resource = TuitionTypeResource::class;
}
