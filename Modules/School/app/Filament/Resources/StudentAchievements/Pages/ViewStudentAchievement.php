<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\School\Filament\Resources\StudentAchievements\StudentAchievementResource;
use Modules\School\Models\StudentAchievement;

class ViewStudentAchievement extends ViewRecord
{
    protected static string $resource = StudentAchievementResource::class;

    protected function getHeaderActions(): array
    {
        /** @var StudentAchievement $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('school.student-achievements.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
