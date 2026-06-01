<?php

namespace Modules\Event\Filament\Resources\EventCertificates\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Event\Filament\Resources\EventCertificates\EventCertificateResource;
use Modules\Event\Models\EventCertificate;

class ViewEventCertificate extends ViewRecord
{
    protected static string $resource = EventCertificateResource::class;

    protected function getHeaderActions(): array
    {
        /** @var EventCertificate $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('event.certificates.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
