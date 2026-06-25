<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Models\Book;
use Modules\Library\Models\Member;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

class OpacBookDetailService
{
    public function __construct(
        private BrandLogoResolver $brandLogoResolver,
    ) {}

    public function render(Tenant $tenant, Book $book, ?Organization $organization = null): View
    {
        $book->load(['category', 'copies', 'reservations.member']);
        $relatedBooks = Book::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', '!=', $book->id)
            ->where('is_active', true)
            ->when($organization !== null, function (Builder $query) use ($organization): void {
                OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);
            })
            ->when($book->book_category_id !== null, fn (Builder $query) => $query->where('book_category_id', $book->book_category_id))
            ->orderByDesc('available_copies')
            ->orderBy('title')
            ->limit(4)
            ->get(['id', 'title', 'available_copies', 'total_copies']);
        $members = auth()->check()
            ? Member::query()
                ->where('tenant_id', $tenant->id)
                ->when($organization !== null, function (Builder $query) use ($organization): void {
                    OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);
                })
                ->where('user_id', auth()->id())
                ->orderBy('member_number')
                ->get()
            : collect();

        return view('library::opac.show', [
            'tenant' => $tenant,
            'organization' => $organization,
            'book' => $book,
            'members' => $members,
            'relatedBooks' => $relatedBooks,
            'brandLogoUrl' => $this->brandLogoResolver->resolve($tenant, $organization),
        ]);
    }
}
