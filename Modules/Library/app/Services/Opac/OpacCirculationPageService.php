<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Data\OpacCirculationSearchFilter;
use Modules\Library\Enums\LoanStatus;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

class OpacCirculationPageService
{
    public function __construct(
        private BrandLogoResolver $brandLogoResolver,
    ) {}

    public function render(Tenant $tenant, OpacCirculationSearchFilter $filter, ?Organization $organization = null): View
    {
        $members = collect();
        if ($filter->memberQuery !== '') {
            $members = Member::query()
                ->with('user')
                ->where('tenant_id', $tenant->id)
                ->when($organization !== null, function (Builder $query) use ($organization): void {
                    OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);
                })
                ->where(function (Builder $query) use ($filter): void {
                    $query->where('member_number', 'like', "%{$filter->memberQuery}%")
                        ->orWhere('status', 'like', "%{$filter->memberQuery}%")
                        ->orWhereHas('user', function (Builder $inner) use ($filter): void {
                            $inner->where('name', 'like', "%{$filter->memberQuery}%")
                                ->orWhere('email', 'like', "%{$filter->memberQuery}%");
                        });
                })
                ->orderBy('member_number')
                ->limit(12)
                ->get();
        }

        $copies = collect();
        $activeLoan = null;
        if ($filter->itemQuery !== '') {
            $copies = BookCopy::query()
                ->with(['book'])
                ->where('tenant_id', $tenant->id)
                ->when($organization !== null, function (Builder $query) use ($organization): void {
                    OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);
                })
                ->where(function (Builder $query) use ($filter): void {
                    $query->where('barcode', 'like', "%{$filter->itemQuery}%")
                        ->orWhere('copy_number', 'like', "%{$filter->itemQuery}%")
                        ->orWhereHas('book', fn (Builder $bookQuery) => $bookQuery->where('title', 'like', "%{$filter->itemQuery}%"));
                })
                ->orderBy('copy_number')
                ->limit(12)
                ->get();

            $activeLoan = Loan::query()
                ->with(['member.user', 'bookCopy.book'])
                ->where('tenant_id', $tenant->id)
                ->whereNull('return_date')
                ->whereIn('status', LoanStatus::activeValues())
                ->whereHas('bookCopy', function (Builder $query) use ($filter, $organization): void {
                    $query->where(function (Builder $builder) use ($filter): void {
                        $builder->where('barcode', 'like', "%{$filter->itemQuery}%")
                            ->orWhere('copy_number', 'like', "%{$filter->itemQuery}%");
                    });

                    if ($organization !== null) {
                        OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);
                    }
                })
                ->latest('loan_date')
                ->first();
        }

        $recentLoans = Loan::query()
            ->with(['member.user', 'bookCopy.book'])
            ->where('tenant_id', $tenant->id)
            ->when($organization !== null, function (Builder $query) use ($organization): void {
                OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);
            })
            ->latest('updated_at')
            ->limit(6)
            ->get();

        return view('library::opac.circulation', [
            'tenant' => $tenant,
            'organization' => $organization,
            'brandLogoUrl' => $this->brandLogoResolver->resolve($tenant, $organization),
            'memberQuery' => $filter->memberQuery,
            'itemQuery' => $filter->itemQuery,
            'scannerMode' => $filter->scannerMode,
            'members' => $members,
            'copies' => $copies,
            'activeLoan' => $activeLoan,
            'recentLoans' => $recentLoans,
        ]);
    }
}
