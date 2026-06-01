<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\StudyResults\StudyResultResource;
use Modules\Campus\Models\StudyResult;
use Modules\Core\Support\FilamentUi;

class ViewStudyResult extends ViewRecord
{
    protected static string $resource = StudyResultResource::class;

    protected function getHeaderActions(): array
    {
        /** @var StudyResult $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadStudyResultPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('campus.study-results.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
