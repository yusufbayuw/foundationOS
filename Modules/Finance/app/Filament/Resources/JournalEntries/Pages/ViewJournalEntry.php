<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Pages;

use App\Support\TypedValue;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Filament\Resources\JournalEntries\JournalEntryResource;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Services\FinanceControlService;
use Throwable;

class ViewJournalEntry extends ViewRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        /** @var JournalEntry $record */
        $record = $this->getRecord();

        return [
            Action::make('postJournal')
                ->label(FilamentUi::text('Post Journal'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (): bool => ! $record->is_posted && ! $record->is_reversed)
                ->form([
                    Textarea::make('notes')->label(FilamentUi::text('Posting Notes'))->rows(3),
                ])
                ->action(function (array $data) use ($record): void {
                    try {
                        /** @var User $user */
                        $user = auth()->user();
                        $notes = TypedValue::string($data['notes'] ?? '');
                        app(FinanceControlService::class)->postJournalEntry($record, $user, $notes !== '' ? $notes : null);
                        Notification::make()->title('Journal entry posted.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to post journal entry.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            Action::make('reverseJournal')
                ->label(FilamentUi::text('Reverse Journal'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->visible(fn (): bool => $record->is_posted && ! $record->is_reversed)
                ->form([
                    Textarea::make('reason')->label(FilamentUi::text('Reversal Reason'))->rows(3)->required(),
                ])
                ->action(function (array $data) use ($record): void {
                    try {
                        /** @var User $user */
                        $user = auth()->user();
                        app(FinanceControlService::class)->reverseJournalEntry($record, $user, TypedValue::string($data['reason']));
                        Notification::make()->title('Journal entry reversed.')->success()->send();
                        $this->record = $this->getRecord()->fresh();
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->title('Failed to reverse journal entry.')->body($exception->getMessage())->danger()->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
