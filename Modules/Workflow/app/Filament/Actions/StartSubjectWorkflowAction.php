<?php

namespace Modules\Workflow\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Support\Notifications\PanelNotification;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;
use Modules\Workflow\Services\WorkflowSubjectPageService;
use Throwable;

class StartSubjectWorkflowAction
{
    /**
     * @param  ViewRecord<Model>  $page
     * @param  Closure(): bool|null  $visibleWhen
     */
    public static function make(
        ViewRecord $page,
        string $successTitleEnglish,
        string $failureTitleEnglish,
        ?Closure $visibleWhen = null,
    ): Action {
        return Action::make('startWorkflow')
            ->label(FilamentUi::text('Start Approval Workflow'))
            ->icon('heroicon-o-play')
            ->color('primary')
            ->authorize(function () use ($page): bool {
                $record = $page->getRecord();

                return auth()->user()?->can('update', $record) ?? false;
            })
            ->visible(function () use ($page, $visibleWhen): bool {
                $record = $page->getRecord();
                $service = app(WorkflowSubjectPageService::class);

                if ($service->hasActiveInstance($record)) {
                    return false;
                }

                if ($visibleWhen !== null && ! $visibleWhen()) {
                    return false;
                }

                if (method_exists($record, 'isLockedForMutation') && $record->isLockedForMutation()) {
                    return false;
                }

                return auth()->user()?->can('update', $record) ?? false;
            })
            ->action(function () use ($page, $successTitleEnglish, $failureTitleEnglish): void {
                try {
                    /** @var User $user */
                    $user = auth()->user();
                    $record = $page->getRecord();

                    $instance = app(WorkflowSubjectPageService::class)->startApprovalWorkflow(
                        $record,
                        $user,
                        Filament::getTenant(),
                    );

                    PanelNotification::success($successTitleEnglish)->send();

                    $page->redirect(WorkflowInstanceResource::getUrl('view', ['record' => $instance]));
                } catch (Throwable $exception) {
                    report($exception);

                    PanelNotification::danger($failureTitleEnglish, $exception->getMessage())->send();
                }
            });
    }
}
