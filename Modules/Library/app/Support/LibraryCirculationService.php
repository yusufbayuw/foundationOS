<?php

namespace Modules\Library\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookReservation;
use Modules\Library\Models\Fine;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;

class LibraryCirculationService
{
    public function __construct(
        protected CirculationPolicyResolver $policyResolver,
    ) {}

    public function placeReservation(Member $member, Book $book, ?string $notes = null): BookReservation
    {
        $existing = BookReservation::query()
            ->where('tenant_id', $member->tenant_id)
            ->where('book_id', $book->id)
            ->where('member_id', $member->id)
            ->whereIn('status', ['pending', 'ready'])
            ->first();

        if ($existing) {
            return $existing;
        }

        $nextQueue = (int) BookReservation::query()
            ->where('tenant_id', $member->tenant_id)
            ->where('book_id', $book->id)
            ->max('queue_position') + 1;

        return BookReservation::query()->create([
            'tenant_id' => (int) $member->tenant_id,
            'organization_id' => $member->organization_id,
            'book_id' => (int) $book->id,
            'member_id' => (int) $member->id,
            'queue_position' => max(1, $nextQueue),
            'status' => 'pending',
            'requested_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function recalculateLoanFine(Loan $loan, ?Carbon $asOf = null): float
    {
        $loan->loadMissing(['member', 'bookCopy.book']);

        if (! $loan->member) {
            return 0.0;
        }

        $policy = $this->policyResolver->resolveForMember($loan->member);
        $comparisonDate = $loan->return_date
            ? Carbon::parse($loan->return_date)
            : ($asOf ?: now());
        $dueDate = Carbon::parse($loan->due_date);
        $lateDays = max(0, $dueDate->diffInDays($comparisonDate, false));
        $chargeableDays = max(0, $lateDays - (int) $policy['grace_period_days']);
        $amount = round($chargeableDays * (float) $policy['fine_per_day'], 2);

        DB::transaction(function () use ($loan, $amount): void {
            $resolvedStatus = $loan->return_date
                ? (in_array($loan->status, ['lost', 'damaged'], true) ? $loan->status : 'returned')
                : ($amount > 0 && now()->toDateString() > (string) $loan->due_date ? 'overdue' : $loan->status);

            $loan->forceFill([
                'fine_amount' => $amount,
                'fine_status' => $amount <= 0 ? 'none' : (($loan->fine_paid ?? 0) >= $amount ? 'paid' : 'unpaid'),
                'status' => $resolvedStatus,
            ])->save();

            if ($amount <= 0) {
                Fine::query()
                    ->where('loan_id', $loan->id)
                    ->where('fine_type', 'late_return')
                    ->delete();

                return;
            }

            Fine::query()->updateOrCreate(
                [
                    'tenant_id' => (int) $loan->tenant_id,
                    'loan_id' => (int) $loan->id,
                    'fine_type' => 'late_return',
                ],
                [
                    'organization_id' => $loan->organization_id,
                    'amount' => $amount,
                    'paid_amount' => (float) ($loan->fine_paid ?? 0),
                    'status' => (($loan->fine_paid ?? 0) >= $amount) ? 'paid' : 'unpaid',
                    'issued_at' => now()->toDateString(),
                ],
            );
        });

        $this->refreshMemberCounters((int) $loan->member_id);

        return $amount;
    }

    public function refreshMemberCounters(int $memberId): void
    {
        $member = Member::query()->find($memberId);

        if (! $member) {
            return;
        }

        $loanQuery = Loan::query()->where('member_id', $member->id);
        $fineQuery = Fine::query()->whereHas('loan', fn ($query) => $query->where('member_id', $member->id));

        $member->forceFill([
            'total_loans_count' => (int) $loanQuery->count(),
            'current_loans_count' => (int) Loan::query()
                ->where('member_id', $member->id)
                ->whereIn('status', ['borrowed', 'overdue'])
                ->whereNull('return_date')
                ->count(),
            'total_fines' => (float) ($fineQuery->sum('amount') ?: 0),
            'unpaid_fines' => (float) ($fineQuery->where('status', '!=', 'paid')->sum(DB::raw('amount - paid_amount')) ?: 0),
        ])->save();
    }

    public function refreshBookAvailability(int $bookId): void
    {
        $book = Book::query()->find($bookId);

        if (! $book) {
            return;
        }

        $total = $book->copies()->count();
        $borrowed = Loan::query()
            ->whereNull('return_date')
            ->whereIn('status', ['borrowed', 'overdue'])
            ->whereHas('bookCopy', fn ($query) => $query->where('book_id', $book->id))
            ->count();

        $book->forceFill([
            'total_copies' => $total,
            'available_copies' => max(0, $total - $borrowed),
        ])->save();

        $this->markNextReservationReady($book);
    }

    public function markNextReservationReady(Book $book): ?BookReservation
    {
        if ((int) $book->available_copies <= 0) {
            return null;
        }

        $reservation = BookReservation::query()
            ->where('tenant_id', $book->tenant_id)
            ->where('book_id', $book->id)
            ->where('status', 'pending')
            ->orderBy('queue_position')
            ->orderBy('requested_at')
            ->first();

        if (! $reservation) {
            return null;
        }

        $reservation->forceFill([
            'status' => 'ready',
            'ready_at' => now(),
            'expires_at' => now()->addDays(2),
        ])->save();

        return $reservation;
    }
}
