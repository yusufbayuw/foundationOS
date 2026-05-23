<?php

namespace App\Filament\Parent\Resources\ChildAttendances\Pages;

use App\Filament\Parent\Resources\ChildAttendances\ChildAttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListChildAttendances extends ListRecords
{
    protected static string $resource = ChildAttendanceResource::class;
}
