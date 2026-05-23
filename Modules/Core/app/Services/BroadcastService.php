<?php

namespace Modules\Core\Services;

use Illuminate\Support\Collection;
use Modules\Core\Models\Broadcast;
use Modules\Core\Models\User;
use Modules\Messaging\Services\NotificationDispatcher;

class BroadcastService
{
    public function __construct(
        protected NotificationDispatcher $dispatcher,
    ) {}

    /**
     * @param  Collection<int, User>  $users
     */
    public function send(Broadcast $broadcast, Collection $users): void
    {
        $channels = $broadcast->channels ?? ['database'];

        foreach ($users as $user) {
            $this->dispatcher->dispatch(
                user: $user,
                category: 'broadcast',
                subject: $broadcast->subject,
                body: $broadcast->body,
                channels: $channels,
                idempotencyKey: "broadcast:{$broadcast->getKey()}:user:{$user->getKey()}",
            );
        }

        $broadcast->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
