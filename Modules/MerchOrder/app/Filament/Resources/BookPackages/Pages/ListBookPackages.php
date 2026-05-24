<?php

namespace Modules\MerchOrder\Filament\Resources\BookPackages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\MerchOrder\Filament\Resources\BookPackages\BookPackageResource;

class ListBookPackages extends ListRecords
{
    protected static string $resource = BookPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
