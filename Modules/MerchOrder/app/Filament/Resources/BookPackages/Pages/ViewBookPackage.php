<?php

namespace Modules\MerchOrder\Filament\Resources\BookPackages\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\MerchOrder\Filament\Resources\BookPackages\BookPackageResource;

class ViewBookPackage extends ViewRecord
{
    protected static string $resource = BookPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
