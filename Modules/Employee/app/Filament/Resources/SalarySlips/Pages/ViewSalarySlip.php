<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Enums\SalarySlipStatus;
use Modules\Employee\Filament\Resources\SalarySlips\SalarySlipResource;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Services\PayrollCalculationService;
use Throwable;

class ViewSalarySlip extends ViewRecord
{
    protected static string $resource = SalarySlipResource::class;

    protected function getHeaderActions(): array
    {
        /** @var SalarySlip $record */
        $record = $this->getRecord();

        return [
            Action::make('markPaid')
                ->label('Tandai Dibayar')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn (): bool => $record->status === SalarySlipStatus::Processed)
                ->form([
                    TextInput::make('paid_via')
                        ->label('Metode Pembayaran')
                        ->placeholder('cth: Transfer BCA')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        app(PayrollCalculationService::class)->markPaid(
                            $this->getRecord(),
                            $data['paid_via'],
                        );
                        Notification::make()->title('Slip gaji ditandai sebagai dibayar.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $e) {
                        Notification::make()->title('Gagal memproses pembayaran.')->body($e->getMessage())->danger()->send();
                    }
                }),

            Action::make('cancel')
                ->label('Batalkan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => in_array($record->status, [SalarySlipStatus::Draft, SalarySlipStatus::Processed]))
                ->requiresConfirmation()
                ->modalHeading('Batalkan Slip Gaji?')
                ->modalDescription('Slip gaji yang sudah dibatalkan tidak dapat diubah kembali.')
                ->action(function (): void {
                    try {
                        app(PayrollCalculationService::class)->cancel($this->getRecord());
                        Notification::make()->title('Slip gaji dibatalkan.')->warning()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $e) {
                        Notification::make()->title('Gagal membatalkan slip gaji.')->body($e->getMessage())->danger()->send();
                    }
                }),

            EditAction::make()
                ->visible(fn (): bool => ! $record->isLockedForMutation()),
        ];
    }
}
