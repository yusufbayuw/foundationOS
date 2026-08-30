<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Modules\Procurement\Models\PurchaseOrder;
use Throwable;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        /** @var PurchaseOrder $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('procurement.purchase-orders.pdf', $record))
                ->openUrlInNewTab(),
            Action::make('approvePurchaseOrder')
                ->label(FilamentUi::text('Approve PO'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->authorize('update')
                ->visible(fn (): bool => $this->record->status === 'draft')
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        $this->record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Purchase order approved.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to approve purchase order.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            Action::make('rejectPurchaseOrder')
                ->label(FilamentUi::text('Reject PO'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->authorize('update')
                ->visible(fn (): bool => $this->record->status === 'draft')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label(FilamentUi::text('Rejection Reason'))
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $this->record->update([
                            'status' => 'rejected',
                            'notes' => $data['rejection_reason'],
                        ]);
                        Notification::make()->title('Purchase order rejected.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to reject purchase order.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
