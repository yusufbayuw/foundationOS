<?php

namespace Modules\MerchOrder\Filament\Resources\UniformPackages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\MerchOrder\Filament\Resources\UniformPackages\UniformPackageResource;

class ListUniformPackages extends ListRecords
{
    protected static string $resource = UniformPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
