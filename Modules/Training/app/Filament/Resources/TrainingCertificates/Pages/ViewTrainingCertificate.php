<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Training\Filament\Resources\TrainingCertificates\TrainingCertificateResource;
use Modules\Training\Models\TrainingCertificate;

class ViewTrainingCertificate extends ViewRecord
{
    protected static string $resource = TrainingCertificateResource::class;

    protected function getHeaderActions(): array
    {
        /** @var TrainingCertificate $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('training.certificates.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
