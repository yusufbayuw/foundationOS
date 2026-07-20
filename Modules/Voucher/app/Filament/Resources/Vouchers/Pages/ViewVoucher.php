<?php

namespace Modules\Voucher\Filament\Resources\Vouchers\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Voucher\Filament\Resources\Vouchers\VoucherResource;

class ViewVoucher extends ViewRecord
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
