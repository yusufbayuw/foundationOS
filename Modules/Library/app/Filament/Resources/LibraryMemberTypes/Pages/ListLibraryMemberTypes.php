<?php

namespace Modules\Library\Filament\Resources\LibraryMemberTypes\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\LibraryMemberTypes\LibraryMemberTypeResource;

class ListLibraryMemberTypes extends ListRecords
{
    protected static string $resource = LibraryMemberTypeResource::class;
}
