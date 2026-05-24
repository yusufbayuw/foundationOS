<?php

namespace Modules\MerchOrder\Filament\Resources\BookPackages\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\MerchOrder\Filament\Resources\BookPackages\BookPackageResource;

class CreateBookPackage extends CreateRecord
{
    protected static string $resource = BookPackageResource::class;
}
