<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Enums\BookCopyStatus;
use Modules\Library\Enums\LoanStatus;
use Modules\Library\Models\Loan;
use Modules\Library\Support\LibraryCirculationService;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

class OpacQuickReturnService
{
    public function returnLoan(
        Tenant $tenant,
        LibraryCirculationService $circulationService,
        int $loanId,
        ?Organization $organization = null,
    ): RedirectResponse {
        $loan = Loan::query()
            ->with(['member', 'bookCopy'])
            ->where('tenant_id', $tenant->id)
            ->whereKey($loanId)
            ->when($organization !== null, function (Builder $query) use ($organization): void {
                OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);
            })
            ->firstOrFail();

        abort_if(! $loan->isActive(), 422, 'Loan ini tidak lagi aktif untuk proses pengembalian.');

        DB::transaction(function () use ($loan): void {
            $loan->forceFill([
                'return_date' => now()->toDateString(),
                'returned_by' => auth()->id(),
                'status' => LoanStatus::Returned->value,
            ])->save();

            if ($loan->bookCopy) {
                $loan->bookCopy->forceFill(['status' => BookCopyStatus::Available->value])->save();
            }
        });

        $circulationService->recalculateLoanFine($loan->fresh(['member', 'bookCopy.book']));

        if ($loan->member_id) {
            $circulationService->refreshMemberCounters((int) $loan->member_id);
        }
        if ($loan->bookCopy?->book_id) {
            $circulationService->refreshBookAvailability((int) $loan->bookCopy->book_id);
        }

        return redirect()->back()->with('status', 'Pengembalian cepat berhasil diproses.');
    }
}
