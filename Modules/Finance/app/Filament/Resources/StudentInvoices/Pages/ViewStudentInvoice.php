<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Filament\Resources\StudentInvoices\StudentInvoiceResource;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Services\FinanceControlService;
use Throwable;

class ViewStudentInvoice extends ViewRecord
{
    protected static string $resource = StudentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var StudentInvoice $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('finance.student-invoices.pdf', $record))
                ->openUrlInNewTab(),
            Action::make('markIssued')
                ->label(FilamentUi::text('Mark Issued'))
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn (): bool => $record->status === 'draft')
                ->action(function (): void {
                    try {
                        /** @var User $user */
                        $user = auth()->user();
                        app(FinanceControlService::class)->markInvoiceIssued($this->getRecord(), $user);

                        Notification::make()->title('Invoice marked as issued.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to issue invoice.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
