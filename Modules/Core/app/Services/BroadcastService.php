<?php

namespace Modules\Core\Services;

use App\Support\TypedValue;
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
        $channelList = is_array($broadcast->channels)
            ? array_values(array_filter($broadcast->channels, is_string(...)))
            : ['database'];

        foreach ($users as $user) {
            $this->dispatcher->dispatch(
                user: $user,
                category: 'broadcast',
                subject: $broadcast->subject,
                body: $broadcast->body,
                channels: $channelList,
                idempotencyKey: 'broadcast:'.TypedValue::string($broadcast->getKey()).':user:'.TypedValue::string($user->getKey()),
            );
        }

        $broadcast->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
