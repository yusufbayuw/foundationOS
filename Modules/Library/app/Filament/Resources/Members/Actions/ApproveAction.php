<?php

namespace Modules\Library\Filament\Resources\Members\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Modules\Core\Models\User;
use Modules\Core\Support\NotificationService;
use Modules\Library\Models\Member;

class ApproveAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Approve')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (Member $record): bool => $record->status === 'pending')
            ->action(function (Member $record): void {
                $record->update([
                    'status' => 'approved',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                    'rejection_reason' => null,
                ]);

                if ($record->user instanceof User) {
                    NotificationService::memberApproved($record, $record->user);
                }

                Notification::make()
                    ->title('Member approved.')
                    ->success()
                    ->send();
            });
    }
}
