<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use Modules\Procurement\Models\GoodsReceipt;

class ViewGoodsReceipt extends ViewRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        /** @var GoodsReceipt $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('procurement.goods-receipts.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
