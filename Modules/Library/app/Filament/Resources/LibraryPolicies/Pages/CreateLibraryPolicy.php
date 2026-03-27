<?php

namespace Modules\Library\Filament\Resources\LibraryPolicies\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\LibraryPolicies\LibraryPolicyResource;

class CreateLibraryPolicy extends CreateRecord
{
    protected static string $resource = LibraryPolicyResource::class;
}
