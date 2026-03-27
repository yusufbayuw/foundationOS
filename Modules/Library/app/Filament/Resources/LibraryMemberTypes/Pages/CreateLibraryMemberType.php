<?php

namespace Modules\Library\Filament\Resources\LibraryMemberTypes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\LibraryMemberTypes\LibraryMemberTypeResource;

class CreateLibraryMemberType extends CreateRecord
{
    protected static string $resource = LibraryMemberTypeResource::class;
}
