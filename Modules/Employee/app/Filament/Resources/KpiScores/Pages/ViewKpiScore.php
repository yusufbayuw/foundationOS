<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Models\User;
use Modules\Employee\Enums\KpiScoreStatus;
use Modules\Employee\Filament\Resources\KpiScores\KpiScoreResource;
use Modules\Employee\Models\KpiScore;
use Modules\Employee\Services\KpiScoringService;
use Throwable;

class ViewKpiScore extends ViewRecord
{
    protected static string $resource = KpiScoreResource::class;

    protected function getHeaderActions(): array
    {
        /** @var KpiScore $record */
        $record = $this->getRecord();

        return [
            Action::make('submit')
                ->label('Ajukan untuk Evaluasi')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn (): bool => $record->status === KpiScoreStatus::Draft)
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        app(KpiScoringService::class)->submit($this->getRecord());
                        Notification::make()->title('KPI diajukan untuk evaluasi.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $e) {
                        Notification::make()->title('Gagal mengajukan KPI.')->body($e->getMessage())->danger()->send();
                    }
                }),

            Action::make('evaluate')
                ->label('Tandai Dievaluasi')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('warning')
                ->visible(fn (): bool => $record->status === KpiScoreStatus::Submitted)
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        /** @var User $user */
                        $user = auth()->user();
                        app(KpiScoringService::class)->evaluate($this->getRecord(), $user);
                        Notification::make()->title('KPI ditandai sebagai telah dievaluasi.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $e) {
                        Notification::make()->title('Gagal mengevaluasi KPI.')->body($e->getMessage())->danger()->send();
                    }
                }),

            Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $record->status === KpiScoreStatus::Evaluated)
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        app(KpiScoringService::class)->approve($this->getRecord());
                        Notification::make()->title('KPI disetujui.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $e) {
                        Notification::make()->title('Gagal menyetujui KPI.')->body($e->getMessage())->danger()->send();
                    }
                }),

            EditAction::make()
                ->visible(fn (): bool => ! $record->isLockedForMutation()),
        ];
    }
}
