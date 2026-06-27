<?php

namespace Modules\Legal\Listeners;

use Modules\Core\Models\User;
use Modules\Legal\Events\ContractExpiringSoon;
use Modules\Messaging\Services\NotificationDispatcher;

class SendContractExpiringNotification
{
    public function __construct(
        protected NotificationDispatcher $dispatcher,
    ) {}

    public function handle(ContractExpiringSoon $event): void
    {
        $contract = $event->contract;
        $ownerUserId = data_get($contract->meta, 'owner_user_id');

        if (! $ownerUserId) {
            return;
        }

        $user = User::query()->find($ownerUserId);

        if (! $user instanceof User) {
            return;
        }

        $this->dispatcher->dispatch(
            user: $user,
            category: 'legal.contract_expiring',
            subject: 'Contract expiring soon',
            body: sprintf('Contract %s expires on %s.', $contract->name, $contract->expires_at?->toDateString()),
            channels: ['database'],
            idempotencyKey: 'contract-expiring-'.$contract->getKey().'-'.$contract->expires_at?->toDateString(),
        );
    }
}
