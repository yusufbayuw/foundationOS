<?php

namespace Modules\Workflow\Filament\Actions;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;
use Modules\Workflow\Services\WorkflowSubjectPageService;

class OpenActiveSubjectWorkflowAction
{
    /**
     * @param  ViewRecord<Model>  $page
     */
    public static function make(ViewRecord $page): Action
    {
        return Action::make('openWorkflow')
            ->label(FilamentUi::text('Open Active Workflow'))
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->color('gray')
            ->visible(function () use ($page): bool {
                $record = $page->getRecord();

                return app(WorkflowSubjectPageService::class)->hasActiveInstance($record);
            })
            ->url(function () use ($page): string {
                $record = $page->getRecord();
                $instance = app(WorkflowSubjectPageService::class)->findActiveInstance($record);

                if ($instance === null) {
                    abort(404);
                }

                return WorkflowInstanceResource::getUrl('view', ['record' => $instance]);
            });
    }
}
