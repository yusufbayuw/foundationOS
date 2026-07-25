<?php

namespace Modules\Voucher\Filament\Resources\Vouchers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Voucher\Filament\Resources\Vouchers\VoucherResource;

class CreateVoucher extends CreateRecord
{
    protected static string $resource = VoucherResource::class;
}
