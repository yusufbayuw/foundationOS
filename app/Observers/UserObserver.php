<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use Illuminate\Support\Carbon;
use Modules\Core\Models\User;

class UserObserver
{
    public function __construct(protected MoodleOutboxService $outbox) {}

    public function created(User $user): void
    {
        $this->enqueue($user, MoodleOutboxService::ACTION_UPSERT);
    }

    public function updated(User $user): void
    {
        $relevant = [
            'name',
            'username',
            'email',
            'status',
            'timezone',
            'locale',
            'deleted_at',
        ];

        if (! $user->wasChanged($relevant)) {
            return;
        }

        $action = $this->shouldDeactivate($user)
            ? MoodleOutboxService::ACTION_DEACTIVATE
            : MoodleOutboxService::ACTION_UPSERT;

        $this->enqueue($user, $action);
    }

    public function deleted(User $user): void
    {
        $this->enqueue($user, MoodleOutboxService::ACTION_DEACTIVATE);
    }

    public function restored(User $user): void
    {
        $this->enqueue($user, MoodleOutboxService::ACTION_UPSERT);
    }

    public function forceDeleted(User $user): void
    {
        $this->enqueue($user, MoodleOutboxService::ACTION_DEACTIVATE);
    }

    protected function enqueue(User $user, string $action): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $version = optional($user->updated_at)->timestamp ?? now()->timestamp;
        $dedupe = "user:{$user->id}:{$action}:{$version}";

        $this->outbox->enqueue(
            MoodleOutboxService::ENTITY_USER,
            (int) $user->id,
            null,
            $action,
            [
                'email' => $user->email,
                'status' => $user->status,
                'deleted_at' => (($d = $user->getAttribute('deleted_at')) instanceof Carbon ? $d->toDateTimeString() : null),
            ],
            $dedupe,
        );
    }

    protected function shouldDeactivate(User $user): bool
    {
        if ($user->trashed()) {
            return true;
        }

        return strtolower((string) $user->status) !== 'active';
    }
}
