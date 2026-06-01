<?php

namespace Modules\Consulting\Filament\Resources\EngagementInvoices\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Consulting\Filament\Resources\EngagementInvoices\EngagementInvoiceResource;
use Modules\Consulting\Models\EngagementInvoice;
use Modules\Core\Support\FilamentUi;

class ViewEngagementInvoice extends ViewRecord
{
    protected static string $resource = EngagementInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var EngagementInvoice $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('consulting.engagement-invoices.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
