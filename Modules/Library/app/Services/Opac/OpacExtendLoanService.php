<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Enums\LoanStatus;
use Modules\Library\Models\Loan;
use Modules\Library\Support\CirculationPolicyResolver;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

class OpacExtendLoanService
{
    public function extend(
        Tenant $tenant,
        CirculationPolicyResolver $policyResolver,
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

        abort_if(! $loan->isActive(), 422, 'Loan ini tidak lagi aktif untuk diperpanjang.');
        abort_if(! $loan->member, 422, 'Member untuk loan ini tidak ditemukan.');

        $policy = $policyResolver->resolveForMember($loan->member);
        abort_if((int) $loan->extension_count >= (int) $policy['max_extensions'], 422, 'Batas perpanjangan untuk loan ini sudah tercapai.');

        $baseDate = $loan->due_date && $loan->due_date->isFuture()
            ? $loan->due_date->copy()
            : now();

        $loan->forceFill([
            'due_date' => $baseDate->addDays($policy['loan_period_days'])->toDateString(),
            'extension_count' => (int) $loan->extension_count + 1,
            'status' => LoanStatus::Borrowed->value,
        ])->save();

        return redirect()->back()->with('status', 'Perpanjangan loan berhasil diproses.');
    }
}
