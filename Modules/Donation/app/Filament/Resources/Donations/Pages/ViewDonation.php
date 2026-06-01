<?php

namespace Modules\Donation\Filament\Resources\Donations\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Donation\Filament\Resources\Donations\DonationResource;
use Modules\Donation\Models\Donation;

class ViewDonation extends ViewRecord
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Donation $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('donation.receipts.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
