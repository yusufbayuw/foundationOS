<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Http\RedirectResponse;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Models\Book;
use Modules\Library\Models\Member;
use Modules\Library\Support\LibraryCirculationService;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

class OpacReservationService
{
    public function place(
        Tenant $tenant,
        Book $book,
        LibraryCirculationService $circulationService,
        int $memberId,
        ?string $notes,
        ?Organization $organization = null,
    ): RedirectResponse {
        $memberQuery = Member::query()
            ->where('tenant_id', $tenant->id)
            ->whereKey($memberId);

        OrganizationVisibilityQuery::applyTenantWideOrOrganization($memberQuery, $organization);

        if (! auth()->user()?->isGlobalSuperAdmin()) {
            $memberQuery->where('user_id', auth()->id());
        }

        $member = $memberQuery->firstOrFail();

        $circulationService->placeReservation($member, $book, $notes);

        return redirect()
            ->route(
                $organization ? 'library.opac.organization.show' : 'library.opac.show',
                array_filter([
                    'tenant' => $tenant->code,
                    'organization' => $organization?->code,
                    'book' => $book,
                ], fn ($value) => $value !== null),
            )
            ->with('status', 'Reservasi berhasil dibuat.');
    }
}
