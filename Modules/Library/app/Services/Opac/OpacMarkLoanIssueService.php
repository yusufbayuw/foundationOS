<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Enums\LoanIssueType;
use Modules\Library\Models\Loan;
use Modules\Library\Support\LibraryCirculationService;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

class OpacMarkLoanIssueService
{
    public function mark(
        Tenant $tenant,
        LibraryCirculationService $circulationService,
        int $loanId,
        LoanIssueType $issueType,
        ?string $notes,
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

        abort_if(! $loan->isActive(), 422, 'Loan ini tidak lagi aktif untuk ditandai bermasalah.');
        abort_if(! $loan->bookCopy, 422, 'Copy buku untuk loan ini tidak ditemukan.');

        $issueValue = $issueType->value;
        $issueLabel = $issueType->label();

        DB::transaction(function () use ($loan, $notes, $issueValue): void {
            $existingNotes = trim((string) $loan->notes);
            $issueNotes = trim((string) ($notes ?? ''));

            $loan->forceFill([
                'return_date' => now()->toDateString(),
                'returned_by' => auth()->id(),
                'status' => $issueValue,
                'condition_on_return' => $issueValue,
                'notes' => trim(implode("\n", array_filter([
                    $existingNotes,
                    $issueNotes !== '' ? '['.strtoupper($issueValue).'] '.$issueNotes : null,
                ]))),
            ])->save();

            $loan->bookCopy->forceFill([
                'status' => $issueValue,
                'condition' => $issueValue,
                'notes' => trim(implode("\n", array_filter([
                    trim((string) $loan->bookCopy->notes),
                    $issueNotes !== '' ? '['.strtoupper($issueValue).'] '.$issueNotes : null,
                ]))),
            ])->save();
        });

        $circulationService->recalculateLoanFine($loan->fresh(['member', 'bookCopy.book']));

        if ($loan->member_id) {
            $circulationService->refreshMemberCounters((int) $loan->member_id);
        }
        if ($loan->bookCopy?->book_id) {
            $circulationService->refreshBookAvailability((int) $loan->bookCopy->book_id);
        }

        return redirect()->back()->with('status', "Loan berhasil ditandai {$issueLabel}.");
    }
}
