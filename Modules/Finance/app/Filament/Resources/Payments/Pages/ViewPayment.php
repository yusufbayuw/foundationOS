<?php

namespace Modules\Finance\Filament\Resources\Payments\Pages;

use App\Support\TypedValue;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Filament\Resources\Payments\PaymentResource;
use Modules\Finance\Models\Payment;
use Modules\Finance\Services\FinanceControlService;
use Throwable;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Payment $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('finance.payments.pdf', $record))
                ->openUrlInNewTab(),
            Action::make('verifyPayment')
                ->label(FilamentUi::text('Verify Payment'))
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn (): bool => $record->status === 'pending')
                ->form([
                    Textarea::make('notes')->label(FilamentUi::text('Verification Notes'))->rows(3),
                ])
                ->action(function (array $data) use ($record): void {
                    try {
                        /** @var User $user */
                        $user = auth()->user();
                        $notes = TypedValue::string($data['notes'] ?? '');
                        app(FinanceControlService::class)->verifyPayment($record, $user, $notes !== '' ? $notes : null);
                        Notification::make()->title('Payment verified and journal posted.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to verify payment.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            Action::make('rejectPayment')
                ->label(FilamentUi::text('Reject Payment'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $record->status === 'pending')
                ->form([
                    Textarea::make('notes')->label(FilamentUi::text('Rejection Notes'))->rows(3)->required(),
                ])
                ->action(function (array $data) use ($record): void {
                    try {
                        /** @var User $user */
                        $user = auth()->user();
                        $notes = TypedValue::string($data['notes'] ?? '');
                        app(FinanceControlService::class)->rejectPayment($record, $user, $notes !== '' ? $notes : null);
                        Notification::make()->title('Payment rejected.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to reject payment.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
