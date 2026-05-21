<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\SalarySlips\SalarySlipResource;
use Modules\Employee\Models\SalarySlip;

class ViewSalarySlip extends ViewRecord
{
    protected static string $resource = SalarySlipResource::class;

    protected function getHeaderActions(): array
    {
        /** @var SalarySlip $record */
        $record = $this->getRecord();

        return [
            Action::make('approveSalarySlip')
                ->label('Approve Slip')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $record->status === 'draft')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->getRecord()->update(['status' => 'approved']);
                    Notification::make()->title('Salary slip approved.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            Action::make('markAsPaid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->visible(fn (): bool => $record->status === 'approved')
                ->form([
                    DateTimePicker::make('paid_at')
                        ->label('Paid At')
                        ->required()
                        ->default(now()),
                    TextInput::make('paid_via')
                        ->label('Paid Via')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->getRecord()->update([
                        'status' => 'paid',
                        'paid_at' => $data['paid_at'],
                        'paid_via' => $data['paid_via'],
                    ]);
                    Notification::make()->title('Salary slip marked as paid.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            Action::make('sendToEmployee')
                ->label('Send to Employee')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->visible(fn (): bool => $record->status === 'approved')
                ->requiresConfirmation()
                ->modalDescription('This will mark the salary slip as sent to the employee.')
                ->action(function (): void {
                    $this->getRecord()->update([
                        'is_sent' => true,
                        'sent_at' => now(),
                    ]);
                    Notification::make()->title('Salary slip sent to employee.')->success()->send();
                    $this->record = $this->getRecord()->fresh();
                }),

            EditAction::make(),
        ];
    }
}
