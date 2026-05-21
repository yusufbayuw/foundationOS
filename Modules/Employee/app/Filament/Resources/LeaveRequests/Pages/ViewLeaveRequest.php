<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\LeaveRequests\LeaveRequestResource;
use Modules\Employee\Models\LeaveRequest;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        /** @var LeaveRequest $record */
        $record = $this->getRecord();

        return [
            Action::make('submitForApproval')
                ->label('Submit for Approval')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn (): bool => $record->status === 'draft')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->update(['status' => 'pending']);
                    Notification::make()->title('Leave request submitted for approval.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            Action::make('supervisorApprove')
                ->label('Supervisor Approve')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->visible(fn (): bool => $record->status === 'pending' && $record->supervisor_approved_at === null)
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->update([
                        'supervisor_approved_at' => now(),
                    ]);
                    Notification::make()->title('Leave request supervisor-approved.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $record->status === 'pending')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approver_id' => auth()->id(),
                    ]);
                    Notification::make()->title('Leave request approved.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $record->status === 'pending')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->getRecord()->update([
                        'status' => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);
                    Notification::make()->title('Leave request rejected.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            EditAction::make(),
        ];
    }
}
