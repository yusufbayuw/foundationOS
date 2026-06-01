<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Enrollment\Filament\Resources\Applicants\ApplicantResource;
use Modules\Enrollment\Models\Applicant;

class ViewApplicant extends ViewRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Applicant $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadAcceptancePdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->canPrintAcceptanceLetter())
                ->url(fn (): string => route('enrollment.applicants.acceptance.pdf', $record))
                ->openUrlInNewTab(),
            Action::make('downloadRejectionPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->canPrintRejectionLetter())
                ->url(fn (): string => route('enrollment.applicants.rejection.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
