<?php

namespace Modules\Marketplace\Filament\Resources\Sellers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Marketplace\Filament\Resources\Sellers\SellerResource;

class CreateSeller extends CreateRecord
{
    protected static string $resource = SellerResource::class;
}
