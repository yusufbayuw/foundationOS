<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\Vendors\VendorResource;
use Modules\Procurement\Models\Vendor;
use Throwable;

/**
 * @property Vendor $record
 */
class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('blacklistVendor')
                ->label(FilamentUi::text('Blacklist Vendor'))
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn (): bool => ! $this->record->is_blacklisted)
                ->form([
                    Textarea::make('blacklist_reason')
                        ->label(FilamentUi::text('Blacklist Reason'))
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $this->record->update([
                            'is_blacklisted' => true,
                            'blacklist_reason' => $data['blacklist_reason'],
                        ]);
                        Notification::make()->title('Vendor has been blacklisted.')->success()->send();
                        $this->refreshRecord();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to blacklist vendor.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            Action::make('removeFromBlacklist')
                ->label(FilamentUi::text('Remove from Blacklist'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->is_blacklisted)
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        $this->record->update([
                            'is_blacklisted' => false,
                            'blacklist_reason' => null,
                        ]);
                        Notification::make()->title('Vendor removed from blacklist.')->success()->send();
                        $this->refreshRecord();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to remove vendor from blacklist.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            Action::make('deactivateVendor')
                ->label(FilamentUi::text('Deactivate'))
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->visible(fn (): bool => $this->record->is_active && ! $this->record->is_blacklisted)
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        $this->record->update(['is_active' => false]);
                        Notification::make()->title('Vendor deactivated.')->success()->send();
                        $this->refreshRecord();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to deactivate vendor.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            Action::make('activateVendor')
                ->label(FilamentUi::text('Activate'))
                ->icon('heroicon-o-play-circle')
                ->color('success')
                ->visible(fn (): bool => ! $this->record->is_active)
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        $this->record->update(['is_active' => true]);
                        Notification::make()->title('Vendor activated.')->success()->send();
                        $this->refreshRecord();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to activate vendor.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            EditAction::make(),
        ];
    }

    protected function refreshRecord(): void
    {
        $fresh = $this->getRecord()->fresh();

        if ($fresh instanceof Vendor) {
            $this->record = $fresh;
        }
    }
}
