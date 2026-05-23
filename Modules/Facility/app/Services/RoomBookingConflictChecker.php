<?php

namespace Modules\Facility\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Facility\Models\RoomBooking;

class RoomBookingConflictChecker
{
    public function hasConflict(
        int $roomId,
        \DateTimeInterface $startAt,
        \DateTimeInterface $endAt,
        ?int $exceptBookingId = null,
    ): bool {
        return $this->conflictingQuery($roomId, $startAt, $endAt, $exceptBookingId)->exists();
    }

    /**
     * @return Builder<RoomBooking>
     */
    public function conflictingQuery(
        int $roomId,
        \DateTimeInterface $startAt,
        \DateTimeInterface $endAt,
        ?int $exceptBookingId = null,
    ): Builder {
        return RoomBooking::query()
            ->where('room_id', $roomId)
            ->whereNotIn('booking_status', ['rejected', 'cancelled'])
            ->when($exceptBookingId, fn (Builder $q) => $q->whereKeyNot($exceptBookingId))
            ->where(function (Builder $query) use ($startAt, $endAt): void {
                $query->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $startAt);
            });
    }
}
