<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Enums\BookCopyStatus;
use Modules\Library\Enums\FineStatus;
use Modules\Library\Enums\LoanStatus;
use Modules\Library\Enums\MemberStatus;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Library\Support\CirculationPolicyResolver;
use Modules\Library\Support\LibraryCirculationService;

class OpacCheckoutService
{
    public function checkout(
        Tenant $tenant,
        CirculationPolicyResolver $policyResolver,
        LibraryCirculationService $circulationService,
        int $memberId,
        int $bookCopyId,
        ?string $notes,
        ?Organization $organization = null,
    ): RedirectResponse {
        $member = Member::query()
            ->where('tenant_id', $tenant->id)
            ->whereKey($memberId)
            ->visibleForOrganization($organization)
            ->firstOrFail();

        $copy = BookCopy::query()
            ->with('book')
            ->where('tenant_id', $tenant->id)
            ->whereKey($bookCopyId)
            ->visibleForOrganization($organization)
            ->firstOrFail();

        $memberStatus = $member->status instanceof MemberStatus ? $member->status->value : (string) $member->status;
        $copyStatus = $copy->status instanceof BookCopyStatus ? $copy->status->value : (string) $copy->status;

        abort_if($memberStatus !== MemberStatus::Active->value, 422, 'Member tidak aktif untuk transaksi peminjaman.');
        abort_if($copyStatus !== BookCopyStatus::Available->value, 422, 'Copy buku ini tidak tersedia untuk dipinjam.');
        abort_if($copy->book && ! $copy->book->is_active, 422, 'Judul buku ini sedang tidak aktif.');

        $policy = $policyResolver->resolveForMember($member);
        abort_if($member->current_loans_count >= $policy['max_books'], 422, 'Batas peminjaman member sudah tercapai.');

        DB::transaction(function () use ($member, $copy, $policy, $notes): void {
            Loan::query()->create([
                'tenant_id' => (int) $member->tenant_id,
                'organization_id' => $member->organization_id ?? $copy->organization_id,
                'book_copy_id' => (int) $copy->id,
                'member_id' => (int) $member->id,
                'processed_by' => auth()->id(),
                'loan_date' => now()->toDateString(),
                'due_date' => now()->addDays($policy['loan_period_days'])->toDateString(),
                'extension_count' => 0,
                'max_extensions' => $policy['max_extensions'],
                'status' => LoanStatus::Borrowed->value,
                'fine_amount' => 0,
                'fine_paid' => 0,
                'fine_status' => FineStatus::None->value,
                'notes' => $notes,
            ]);

            $copy->forceFill(['status' => BookCopyStatus::Borrowed->value])->save();
        });

        $circulationService->refreshMemberCounters((int) $member->id);
        if ($copy->book_id) {
            $circulationService->refreshBookAvailability((int) $copy->book_id);
        }

        return redirect()->back()->with('status', "Peminjaman berhasil dibuat untuk member {$member->member_number}.");
    }
}
