<?php

namespace Modules\Library\Filament\Resources\Members\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Core\Support\NotificationService;
use Modules\Library\Models\Member;

class RejectAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (Member $record): bool => $record->status === 'pending')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label(FilamentUi::field('rejection_reason'))
                    ->rows(3)
                    ->required(),
            ])
            ->action(function (array $data, Member $record): void {
                $record->update([
                    'status' => 'rejected',
                    'rejection_reason' => $data['rejection_reason'],
                ]);

                if ($record->user instanceof User) {
                    NotificationService::memberRejected($record, $record->user, $data['rejection_reason']);
                }

                Notification::make()
                    ->title('Member rejected.')
                    ->success()
                    ->send();
            });
    }
}
