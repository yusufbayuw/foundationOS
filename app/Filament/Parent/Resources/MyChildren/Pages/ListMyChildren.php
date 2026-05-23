<?php

namespace App\Filament\Parent\Resources\MyChildren\Pages;

use App\Filament\Parent\Resources\MyChildren\MyChildrenResource;
use Filament\Resources\Pages\ListRecords;

class ListMyChildren extends ListRecords
{
    protected static string $resource = MyChildrenResource::class;
}
