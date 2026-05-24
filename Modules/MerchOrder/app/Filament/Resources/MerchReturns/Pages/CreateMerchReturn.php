<?php

namespace Modules\MerchOrder\Filament\Resources\MerchReturns\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\MerchOrder\Filament\Resources\MerchReturns\MerchReturnResource;

class CreateMerchReturn extends CreateRecord
{
    protected static string $resource = MerchReturnResource::class;
}
