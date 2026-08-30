<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
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
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('employee.leave-requests.pdf', $record))
                ->openUrlInNewTab(),
            Action::make('submitForApproval')
                ->label(FilamentUi::text('Submit for Approval'))
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->authorize('update')
                ->visible(fn (): bool => $record->status === 'draft')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->update(['status' => 'pending']);
                    Notification::make()->title('Leave request submitted for approval.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            Action::make('supervisorApprove')
                ->label(FilamentUi::text('Supervisor Approve'))
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->authorize('update')
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
                ->label(FilamentUi::text('Approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->authorize('update')
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
                ->label(FilamentUi::text('Reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->authorize('update')
                ->visible(fn (): bool => $record->status === 'pending')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label(FilamentUi::text('Rejection Reason'))
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
