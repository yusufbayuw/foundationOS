<?php

namespace Modules\MerchOrder\Filament\Resources\UniformPackages\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\MerchOrder\Filament\Resources\UniformPackages\UniformPackageResource;

class ViewUniformPackage extends ViewRecord
{
    protected static string $resource = UniformPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
