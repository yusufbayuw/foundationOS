<?php

namespace Modules\Library\Filament\Resources\LibraryPolicies\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\LibraryPolicies\LibraryPolicyResource;

class ListLibraryPolicies extends ListRecords
{
    protected static string $resource = LibraryPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
