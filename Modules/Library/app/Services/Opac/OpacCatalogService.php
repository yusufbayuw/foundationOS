<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Data\OpacCatalogFilter;
use Modules\Library\Models\Book;
use Modules\Library\Support\LibraryScopeResolver;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

class OpacCatalogService
{
    public function __construct(
        private BrandLogoResolver $brandLogoResolver,
    ) {}

    public function render(Tenant $tenant, OpacCatalogFilter $filter, ?Organization $organization = null): View
    {
        $query = Book::query()->with(['category', 'copies', 'publisher']);
        $query = app(LibraryScopeResolver::class)->apply($query, null, (int) $tenant->id)
            ->where('is_active', true);

        OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization);

        $summaryQuery = clone $query;
        $filterQuery = clone $query;

        if ($filter->categoryId !== null) {
            $query->where('book_category_id', $filter->categoryId);
        }

        if ($filter->publisher !== '') {
            $query->where(function (Builder $builder) use ($filter): void {
                $builder->where('publisher', $filter->publisher)
                    ->orWhereHas('publisher', fn (Builder $inner) => $inner->where('name', $filter->publisher));
            });
        }

        if ($filter->availableOnly) {
            $query->where('available_copies', '>', 0);
        }

        if ($filter->search !== '') {
            $query->where(function (Builder $builder) use ($filter): void {
                $builder->where('title', 'like', "%{$filter->search}%")
                    ->orWhere('isbn', 'like', "%{$filter->search}%")
                    ->orWhere('publisher', 'like', "%{$filter->search}%")
                    ->orWhere('classification_code', 'like', "%{$filter->search}%");
            });
        }

        $featuredCategories = (clone $summaryQuery)
            ->whereNotNull('book_category_id')
            ->selectRaw('book_category_id, COUNT(*) as aggregate')
            ->groupBy('book_category_id')
            ->orderByDesc('aggregate')
            ->with('category')
            ->limit(5)
            ->get()
            ->map(fn (Book $book) => [
                'name' => $book->category?->name ?? 'Tanpa Kategori',
                'total' => (int) $book->aggregate,
            ]);

        $organizations = $organization === null
            ? $tenant->organizations()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'short_name'])
            : collect();

        $categoryOptions = (clone $filterQuery)
            ->whereNotNull('book_category_id')
            ->with('category')
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $publisherOptions = (clone $filterQuery)
            ->with('publisher')
            ->get()
            ->flatMap(fn (Book $book) => array_filter([
                $book->publisher?->name,
                $book->publisher,
            ]))
            ->unique()
            ->sort()
            ->values();

        return view('library::opac.index', [
            'tenant' => $tenant,
            'organization' => $organization,
            'query' => $filter->search,
            'books' => $query->orderBy('title')->paginate(12)->withQueryString(),
            'featuredCategories' => $featuredCategories,
            'organizations' => $organizations,
            'categoryOptions' => $categoryOptions,
            'publisherOptions' => $publisherOptions,
            'selectedCategory' => $filter->categoryId,
            'selectedPublisher' => $filter->publisher,
            'availableOnly' => $filter->availableOnly,
            'stats' => [
                'titles' => (clone $summaryQuery)->count(),
                'copies' => (clone $summaryQuery)->sum('total_copies'),
                'available' => (clone $summaryQuery)->sum('available_copies'),
            ],
            'brandLogoUrl' => $this->brandLogoResolver->resolve($tenant, $organization),
        ]);
    }
}
