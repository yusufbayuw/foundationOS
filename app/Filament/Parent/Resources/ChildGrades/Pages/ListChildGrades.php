<?php

namespace App\Filament\Parent\Resources\ChildGrades\Pages;

use App\Filament\Parent\Resources\ChildGrades\ChildGradeResource;
use Filament\Resources\Pages\ListRecords;

class ListChildGrades extends ListRecords
{
    protected static string $resource = ChildGradeResource::class;
}
