<?php

namespace Modules\Core\Jobs;

use App\Concerns\InteractsWithTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Modules\Core\Models\Broadcast;
use Modules\Core\Models\User;
use Modules\Core\Services\BroadcastService;

class SendBroadcastNotificationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use InteractsWithTenant;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    /**
     * @param  list<int>  $userIds
     */
    public function __construct(public int $broadcastId, public array $userIds)
    {
        $this->captureCurrentTenant();
    }

    public function handle(BroadcastService $broadcastService): void
    {
        $broadcast = Broadcast::query()->find($this->broadcastId);

        if ($broadcast === null) {
            return;
        }

        if ($this->tenantId === null) {
            $this->tenantId = $broadcast->tenant_id;
        }

        /** @var Collection<int, User> $users */
        $users = User::query()->whereIn('id', $this->userIds)->get();

        $broadcastService->deliverToUsers($broadcast, $users);
    }
}
