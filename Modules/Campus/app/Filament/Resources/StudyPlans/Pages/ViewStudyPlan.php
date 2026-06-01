<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\StudyPlans\StudyPlanResource;
use Modules\Campus\Models\StudyPlan;
use Modules\Core\Support\FilamentUi;

class ViewStudyPlan extends ViewRecord
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        /** @var StudyPlan $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadKrsPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('campus.study-plans.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
