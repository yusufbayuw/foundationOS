<?php

namespace Modules\Sales\Filament\Resources\VoucherClaims\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\VoucherClaims\VoucherClaimResource;

class ListVoucherClaims extends ListRecords
{
    protected static string $resource = VoucherClaimResource::class;
}
