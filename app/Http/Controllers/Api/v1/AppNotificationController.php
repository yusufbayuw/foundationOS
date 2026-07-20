<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class AppNotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 20), 100);
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->cursorPaginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => collect($notifications->items())->map(fn (DatabaseNotification $notification): array => $this->serialize($notification))->all(),
            'meta' => [
                'per_page' => $notifications->perPage(),
                'has_more' => $notifications->hasMorePages(),
                'next_cursor' => $notifications->nextCursor()?->encode(),
            ],
        ]);
    }

    public function read(Request $request, string $notification): JsonResponse
    {
        $databaseNotification = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $databaseNotification->markAsRead();

        return $this->success($this->serialize($databaseNotification->refresh()));
    }

    /**
     * @return array<string, mixed>
     */
    protected function serialize(DatabaseNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => data_get($notification->data, 'title'),
            'body' => data_get($notification->data, 'body'),
            'data' => $notification->data,
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
        ];
    }
}
