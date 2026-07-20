<?php

namespace Modules\Voucher\Filament\Resources\Vouchers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Voucher\Filament\Resources\Vouchers\VoucherResource;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
