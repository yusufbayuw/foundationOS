<?php

namespace Modules\Boarding\Filament\Resources\Dormitories\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Boarding\Filament\Resources\Dormitories\DormitoryResource;

class ListDormitories extends ListRecords
{
    protected static string $resource = DormitoryResource::class;
}
