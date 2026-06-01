<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\CollageStudents\CollageStudentResource;
use Modules\Campus\Models\CollageStudent;
use Modules\Core\Support\FilamentUi;

class ViewCollageStudent extends ViewRecord
{
    protected static string $resource = CollageStudentResource::class;

    protected function getHeaderActions(): array
    {
        /** @var CollageStudent $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadTranscriptPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('campus.transcript.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
