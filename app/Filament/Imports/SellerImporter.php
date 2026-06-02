<?php

namespace App\Filament\Imports;

use Modules\Marketplace\Models\Seller;

class SellerImporter extends BaseModelImporter
{
    protected static ?string $model = Seller::class;
}
