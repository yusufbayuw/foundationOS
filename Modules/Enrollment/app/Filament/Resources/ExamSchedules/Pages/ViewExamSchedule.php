<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Enrollment\Filament\Resources\ExamSchedules\ExamScheduleResource;
use Modules\Enrollment\Models\ExamSchedule;

class ViewExamSchedule extends ViewRecord
{
    protected static string $resource = ExamScheduleResource::class;

    protected function getHeaderActions(): array
    {
        /** @var ExamSchedule $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('enrollment.exam-schedules.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
