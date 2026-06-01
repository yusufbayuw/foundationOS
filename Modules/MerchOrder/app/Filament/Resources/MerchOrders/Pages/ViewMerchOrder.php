<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\MerchOrder\Filament\Resources\MerchOrders\MerchOrderResource;
use Modules\MerchOrder\Models\MerchOrder;

class ViewMerchOrder extends ViewRecord
{
    protected static string $resource = MerchOrderResource::class;

    protected function getHeaderActions(): array
    {
        /** @var MerchOrder $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('merch.orders.pdf', $record))
                ->openUrlInNewTab(),
        ];
    }
}
