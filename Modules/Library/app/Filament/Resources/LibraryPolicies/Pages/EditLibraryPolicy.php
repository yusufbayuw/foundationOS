<?php

namespace Modules\Library\Filament\Resources\LibraryPolicies\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Library\Filament\Resources\LibraryPolicies\LibraryPolicyResource;

class EditLibraryPolicy extends EditRecord
{
    protected static string $resource = LibraryPolicyResource::class;
}
