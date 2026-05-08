<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Enums\LeaveRequestStatus;
use Modules\Employee\Filament\Resources\LeaveRequests\LeaveRequestResource;
use Modules\Employee\Models\LeaveRequest;
use Throwable;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        /** @var LeaveRequest $record */
        $record = $this->getRecord();

        return [
            // Employee: submit draft
            Action::make('submit')
                ->label('Ajukan')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn (): bool => $record->status === LeaveRequestStatus::Draft)
                ->requiresConfirmation()
                ->modalHeading('Ajukan Permohonan Cuti?')
                ->action(function (): void {
                    $this->getRecord()->forceFill([
                        'status' => LeaveRequestStatus::Submitted,
                    ])->save();
                    Notification::make()->title('Permohonan cuti diajukan.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            // Supervisor: approve at first level
            Action::make('supervisorApprove')
                ->label('Setujui (Supervisor)')
                ->icon('heroicon-o-check')
                ->color('warning')
                ->visible(fn (): bool => $record->status === LeaveRequestStatus::Submitted)
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->forceFill([
                        'status' => LeaveRequestStatus::SupervisorApproved,
                        'supervisor_approved_at' => now(),
                        'supervisor_id' => auth()->id(),
                    ])->save();
                    Notification::make()->title('Disetujui oleh supervisor.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            // HR/Admin: final approval
            Action::make('approve')
                ->label('Setujui (Final)')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => in_array($record->status, [
                    LeaveRequestStatus::Submitted,
                    LeaveRequestStatus::SupervisorApproved,
                ]))
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->forceFill([
                        'status' => LeaveRequestStatus::Approved,
                        'approved_at' => now(),
                        'approver_id' => auth()->id(),
                    ])->save();
                    Notification::make()->title('Permohonan cuti disetujui.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            // Reject
            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => in_array($record->status, [
                    LeaveRequestStatus::Submitted,
                    LeaveRequestStatus::SupervisorApproved,
                ]))
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->getRecord()->forceFill([
                        'status' => LeaveRequestStatus::Rejected,
                        'rejection_reason' => $data['rejection_reason'],
                    ])->save();
                    Notification::make()->title('Permohonan cuti ditolak.')->warning()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            // Cancel (by employee or admin on draft/submitted)
            Action::make('cancel')
                ->label('Batalkan')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->visible(fn (): bool => in_array($record->status, [
                    LeaveRequestStatus::Draft,
                    LeaveRequestStatus::Submitted,
                ]))
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->forceFill([
                        'status' => LeaveRequestStatus::Cancelled,
                    ])->save();
                    Notification::make()->title('Permohonan cuti dibatalkan.')->warning()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            EditAction::make()
                ->visible(fn (): bool => ! $record->isLockedForMutation()),
        ];
    }
}
