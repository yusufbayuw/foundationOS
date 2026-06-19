<?php

namespace Modules\Library\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Library\Support\CirculationPolicyResolver;
use Modules\Library\Support\LibraryCirculationService;
use Modules\Library\Support\LibraryScopeResolver;

class PublicOpacController extends Controller
{
    public function index(Request $request, string $tenant)
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);

        return $this->renderCatalog($request, $tenantModel);
    }

    public function organizationIndex(Request $request, string $tenant, string $organization)
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);

        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null, 404);

        return $this->renderCatalog($request, $tenantModel, $organizationModel);
    }

    public function show(string $tenant, Book $book)
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null && (int) $book->tenant_id === (int) $tenantModel->id, 404);

        return $this->renderBookDetail($tenantModel, $book);
    }

    public function organizationShow(string $tenant, string $organization, Book $book)
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null && (int) $book->tenant_id === (int) $tenantModel->id, 404);

        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null && $this->bookVisibleForOrganization($book, $organizationModel), 404);

        return $this->renderBookDetail($tenantModel, $book, $organizationModel);
    }

    public function reserve(Request $request, string $tenant, Book $book, LibraryCirculationService $circulationService)
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null && (int) $book->tenant_id === (int) $tenantModel->id, 404);

        return $this->handleReservation($request, $tenantModel, $book, $circulationService);
    }

    public function organizationReserve(Request $request, string $tenant, string $organization, Book $book, LibraryCirculationService $circulationService)
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null && (int) $book->tenant_id === (int) $tenantModel->id, 404);

        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null && $this->bookVisibleForOrganization($book, $organizationModel), 404);

        return $this->handleReservation($request, $tenantModel, $book, $circulationService, $organizationModel);
    }

    public function circulation(Request $request, string $tenant): View
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->renderCirculation($request, $tenantModel);
    }

    public function organizationCirculation(Request $request, string $tenant, string $organization): View
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->renderCirculation($request, $tenantModel, $organizationModel);
    }

    public function checkout(Request $request, string $tenant, CirculationPolicyResolver $policyResolver, LibraryCirculationService $circulationService): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleCheckout($request, $tenantModel, $policyResolver, $circulationService);
    }

    public function organizationCheckout(Request $request, string $tenant, string $organization, CirculationPolicyResolver $policyResolver, LibraryCirculationService $circulationService): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleCheckout($request, $tenantModel, $policyResolver, $circulationService, $organizationModel);
    }

    public function quickReturn(Request $request, string $tenant, LibraryCirculationService $circulationService): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleQuickReturn($request, $tenantModel, $circulationService);
    }

    public function organizationQuickReturn(Request $request, string $tenant, string $organization, LibraryCirculationService $circulationService): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleQuickReturn($request, $tenantModel, $circulationService, $organizationModel);
    }

    public function extendLoan(Request $request, string $tenant, CirculationPolicyResolver $policyResolver): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleExtendLoan($request, $tenantModel, $policyResolver);
    }

    public function organizationExtendLoan(Request $request, string $tenant, string $organization, CirculationPolicyResolver $policyResolver): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleExtendLoan($request, $tenantModel, $policyResolver, $organizationModel);
    }

    public function markIssue(Request $request, string $tenant, LibraryCirculationService $circulationService): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleMarkIssue($request, $tenantModel, $circulationService);
    }

    public function organizationMarkIssue(Request $request, string $tenant, string $organization, LibraryCirculationService $circulationService): RedirectResponse
    {
        $tenantModel = $this->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);
        $organizationModel = $this->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null, 404);
        $this->authorizeCirculation($request->user(), $tenantModel);

        return $this->handleMarkIssue($request, $tenantModel, $circulationService, $organizationModel);
    }

    protected function renderCatalog(Request $request, Tenant $tenantModel, ?Organization $organizationModel = null)
    {
        $search = trim((string) $request->query('q', ''));
        $categoryId = is_numeric($request->query('category')) ? (int) $request->query('category') : null;
        $publisher = trim((string) $request->query('publisher', ''));
        $availableOnly = in_array(strtolower((string) $request->query('available_only', '')), ['1', 'true', 'yes', 'on'], true);

        $query = Book::query()->with(['category', 'copies', 'publisher']);
        $query = app(LibraryScopeResolver::class)->apply($query, null, (int) $tenantModel->id)
            ->where('is_active', true);

        if ($organizationModel) {
            $query->where(function (Builder $builder) use ($organizationModel): void {
                $builder->whereNull('organization_id')
                    ->orWhere('organization_id', $organizationModel->id);
            });
        }

        $summaryQuery = clone $query;
        $filterQuery = clone $query;

        if ($categoryId !== null) {
            $query->where('book_category_id', $categoryId);
        }

        if ($publisher !== '') {
            $query->where(function (Builder $builder) use ($publisher): void {
                $builder->where('publisher', $publisher)
                    ->orWhereHas('publisher', fn (Builder $inner) => $inner->where('name', $publisher));
            });
        }

        if ($availableOnly) {
            $query->where('available_copies', '>', 0);
        }

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('classification_code', 'like', "%{$search}%");
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

        $organizations = $organizationModel === null
            ? $tenantModel->organizations()
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
            'tenant' => $tenantModel,
            'organization' => $organizationModel,
            'query' => $search,
            'books' => $query->orderBy('title')->paginate(12)->withQueryString(),
            'featuredCategories' => $featuredCategories,
            'organizations' => $organizations,
            'categoryOptions' => $categoryOptions,
            'publisherOptions' => $publisherOptions,
            'selectedCategory' => $categoryId,
            'selectedPublisher' => $publisher,
            'availableOnly' => $availableOnly,
            'stats' => [
                'titles' => (clone $summaryQuery)->count(),
                'copies' => (clone $summaryQuery)->sum('total_copies'),
                'available' => (clone $summaryQuery)->sum('available_copies'),
            ],
            'brandLogoUrl' => $this->resolveBrandLogoUrl($tenantModel, $organizationModel),
        ]);
    }

    protected function renderBookDetail(Tenant $tenantModel, Book $book, ?Organization $organizationModel = null)
    {
        $book->load(['category', 'copies', 'reservations.member']);
        $relatedBooks = Book::query()
            ->where('tenant_id', $tenantModel->id)
            ->where('id', '!=', $book->id)
            ->where('is_active', true)
            ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                $query->where(function (Builder $builder) use ($organizationModel): void {
                    $builder->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationModel->id);
                });
            })
            ->when($book->book_category_id !== null, fn (Builder $query) => $query->where('book_category_id', $book->book_category_id))
            ->orderByDesc('available_copies')
            ->orderBy('title')
            ->limit(4)
            ->get(['id', 'title', 'available_copies', 'total_copies']);
        $members = auth()->check()
            ? Member::query()
                ->where('tenant_id', $tenantModel->id)
                ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                    $query->where(function (Builder $builder) use ($organizationModel): void {
                        $builder->whereNull('organization_id')
                            ->orWhere('organization_id', $organizationModel->id);
                    });
                })
                ->where('user_id', auth()->id())
                ->orderBy('member_number')
                ->get()
            : collect();

        return view('library::opac.show', [
            'tenant' => $tenantModel,
            'organization' => $organizationModel,
            'book' => $book,
            'members' => $members,
            'relatedBooks' => $relatedBooks,
            'brandLogoUrl' => $this->resolveBrandLogoUrl($tenantModel, $organizationModel),
        ]);
    }

    protected function handleReservation(Request $request, Tenant $tenantModel, Book $book, LibraryCirculationService $circulationService, ?Organization $organizationModel = null)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $memberQuery = Member::query()
            ->where('tenant_id', $tenantModel->id)
            ->whereKey((int) $validated['member_id']);

        if ($organizationModel !== null) {
            $memberQuery->where(function (Builder $query) use ($organizationModel): void {
                $query->whereNull('organization_id')
                    ->orWhere('organization_id', $organizationModel->id);
            });
        }

        if (! auth()->user()?->isGlobalSuperAdmin()) {
            $memberQuery->where('user_id', auth()->id());
        }

        $member = $memberQuery->firstOrFail();

        $circulationService->placeReservation($member, $book, $validated['notes'] ?? null);

        return redirect()
            ->route(
                $organizationModel ? 'library.opac.organization.show' : 'library.opac.show',
                array_filter([
                    'tenant' => $tenantModel->code,
                    'organization' => $organizationModel?->code,
                    'book' => $book,
                ], fn ($value) => $value !== null),
            )
            ->with('status', 'Reservasi berhasil dibuat.');
    }

    protected function resolveTenant(string $tenant): ?Tenant
    {
        return Tenant::query()
            ->where('code', $tenant)
            ->orWhere('id', $tenant)
            ->first();
    }

    protected function resolveOrganization(Tenant $tenant, string $organization): ?Organization
    {
        return Organization::query()
            ->where('tenant_id', $tenant->id)
            ->where(function (Builder $query) use ($organization): void {
                $query->where('code', $organization)
                    ->orWhere('id', $organization);
            })
            ->first();
    }

    protected function bookVisibleForOrganization(Book $book, Organization $organization): bool
    {
        return $book->organization_id === null || (int) $book->organization_id === (int) $organization->id;
    }

    protected function renderCirculation(Request $request, Tenant $tenantModel, ?Organization $organizationModel = null): View
    {
        $memberQuery = trim((string) $request->query('member_q', ''));
        $itemQuery = trim((string) $request->query('item_q', ''));

        $members = collect();
        if ($memberQuery !== '') {
            $members = Member::query()
                ->with('user')
                ->where('tenant_id', $tenantModel->id)
                ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                    $query->where(function (Builder $builder) use ($organizationModel): void {
                        $builder->whereNull('organization_id')
                            ->orWhere('organization_id', $organizationModel->id);
                    });
                })
                ->where(function (Builder $query) use ($memberQuery): void {
                    $query->where('member_number', 'like', "%{$memberQuery}%")
                        ->orWhere('status', 'like', "%{$memberQuery}%")
                        ->orWhereHas('user', function (Builder $inner) use ($memberQuery): void {
                            $inner->where('name', 'like', "%{$memberQuery}%")
                                ->orWhere('email', 'like', "%{$memberQuery}%");
                        });
                })
                ->orderBy('member_number')
                ->limit(12)
                ->get();
        }

        $copies = collect();
        $activeLoan = null;
        if ($itemQuery !== '') {
            $copies = BookCopy::query()
                ->with(['book'])
                ->where('tenant_id', $tenantModel->id)
                ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                    $query->where(function (Builder $builder) use ($organizationModel): void {
                        $builder->whereNull('organization_id')
                            ->orWhere('organization_id', $organizationModel->id);
                    });
                })
                ->where(function (Builder $query) use ($itemQuery): void {
                    $query->where('barcode', 'like', "%{$itemQuery}%")
                        ->orWhere('copy_number', 'like', "%{$itemQuery}%")
                        ->orWhereHas('book', fn (Builder $bookQuery) => $bookQuery->where('title', 'like', "%{$itemQuery}%"));
                })
                ->orderBy('copy_number')
                ->limit(12)
                ->get();

            $activeLoan = Loan::query()
                ->with(['member.user', 'bookCopy.book'])
                ->where('tenant_id', $tenantModel->id)
                ->whereNull('return_date')
                ->whereIn('status', ['borrowed', 'overdue'])
                ->whereHas('bookCopy', function (Builder $query) use ($itemQuery, $organizationModel): void {
                    $query->where(function (Builder $builder) use ($itemQuery): void {
                        $builder->where('barcode', 'like', "%{$itemQuery}%")
                            ->orWhere('copy_number', 'like', "%{$itemQuery}%");
                    });

                    if ($organizationModel !== null) {
                        $query->where(function (Builder $builder) use ($organizationModel): void {
                            $builder->whereNull('organization_id')
                                ->orWhere('organization_id', $organizationModel->id);
                        });
                    }
                })
                ->latest('loan_date')
                ->first();
        }

        $recentLoans = Loan::query()
            ->with(['member.user', 'bookCopy.book'])
            ->where('tenant_id', $tenantModel->id)
            ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                $query->where(function (Builder $builder) use ($organizationModel): void {
                    $builder->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationModel->id);
                });
            })
            ->latest('updated_at')
            ->limit(6)
            ->get();

        return view('library::opac.circulation', [
            'tenant' => $tenantModel,
            'organization' => $organizationModel,
            'brandLogoUrl' => $this->resolveBrandLogoUrl($tenantModel, $organizationModel),
            'memberQuery' => $memberQuery,
            'itemQuery' => $itemQuery,
            'scannerMode' => in_array(strtolower((string) $request->query('scanner', '1')), ['1', 'true', 'yes', 'on'], true),
            'members' => $members,
            'copies' => $copies,
            'activeLoan' => $activeLoan,
            'recentLoans' => $recentLoans,
        ]);
    }

    protected function handleCheckout(Request $request, Tenant $tenantModel, CirculationPolicyResolver $policyResolver, LibraryCirculationService $circulationService, ?Organization $organizationModel = null): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'integer'],
            'book_copy_id' => ['required', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $member = Member::query()
            ->where('tenant_id', $tenantModel->id)
            ->whereKey((int) $validated['member_id'])
            ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                $query->where(function (Builder $builder) use ($organizationModel): void {
                    $builder->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationModel->id);
                });
            })
            ->firstOrFail();

        $copy = BookCopy::query()
            ->with('book')
            ->where('tenant_id', $tenantModel->id)
            ->whereKey((int) $validated['book_copy_id'])
            ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                $query->where(function (Builder $builder) use ($organizationModel): void {
                    $builder->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationModel->id);
                });
            })
            ->firstOrFail();

        abort_if($member->status !== 'active', 422, 'Member tidak aktif untuk transaksi peminjaman.');
        abort_if($copy->status !== 'available', 422, 'Copy buku ini tidak tersedia untuk dipinjam.');
        abort_if($copy->book && ! $copy->book->is_active, 422, 'Judul buku ini sedang tidak aktif.');

        $policy = $policyResolver->resolveForMember($member);
        abort_if($member->current_loans_count >= $policy['max_books'], 422, 'Batas peminjaman member sudah tercapai.');

        $loan = DB::transaction(function () use ($member, $copy, $policy, $validated, $request): Loan {
            $loan = Loan::query()->create([
                'tenant_id' => (int) $member->tenant_id,
                'organization_id' => $member->organization_id ?? $copy->organization_id,
                'book_copy_id' => (int) $copy->id,
                'member_id' => (int) $member->id,
                'processed_by' => $request->user()?->id,
                'loan_date' => now()->toDateString(),
                'due_date' => now()->addDays($policy['loan_period_days'])->toDateString(),
                'extension_count' => 0,
                'max_extensions' => $policy['max_extensions'],
                'status' => 'borrowed',
                'fine_amount' => 0,
                'fine_paid' => 0,
                'fine_status' => 'none',
                'notes' => $validated['notes'] ?? null,
            ]);

            $copy->forceFill(['status' => 'borrowed'])->save();

            return $loan;
        });

        $circulationService->refreshMemberCounters((int) $member->id);
        if ($copy->book_id) {
            $circulationService->refreshBookAvailability((int) $copy->book_id);
        }

        return redirect()->back()->with('status', "Peminjaman berhasil dibuat untuk member {$member->member_number}.");
    }

    protected function handleQuickReturn(Request $request, Tenant $tenantModel, LibraryCirculationService $circulationService, ?Organization $organizationModel = null): RedirectResponse
    {
        $validated = $request->validate([
            'loan_id' => ['required', 'integer'],
        ]);

        $loan = Loan::query()
            ->with(['member', 'bookCopy'])
            ->where('tenant_id', $tenantModel->id)
            ->whereKey((int) $validated['loan_id'])
            ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                $query->where(function (Builder $builder) use ($organizationModel): void {
                    $builder->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationModel->id);
                });
            })
            ->firstOrFail();

        abort_if(! $loan->isActive(), 422, 'Loan ini tidak lagi aktif untuk proses pengembalian.');

        DB::transaction(function () use ($loan): void {
            $loan->forceFill([
                'return_date' => now()->toDateString(),
                'returned_by' => auth()->id(),
                'status' => 'returned',
            ])->save();

            if ($loan->bookCopy) {
                $loan->bookCopy->forceFill(['status' => 'available'])->save();
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

    protected function handleExtendLoan(Request $request, Tenant $tenantModel, CirculationPolicyResolver $policyResolver, ?Organization $organizationModel = null): RedirectResponse
    {
        $validated = $request->validate([
            'loan_id' => ['required', 'integer'],
        ]);

        $loan = Loan::query()
            ->with(['member', 'bookCopy'])
            ->where('tenant_id', $tenantModel->id)
            ->whereKey((int) $validated['loan_id'])
            ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                $query->where(function (Builder $builder) use ($organizationModel): void {
                    $builder->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationModel->id);
                });
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
            'status' => 'borrowed',
        ])->save();

        return redirect()->back()->with('status', 'Perpanjangan loan berhasil diproses.');
    }

    protected function handleMarkIssue(Request $request, Tenant $tenantModel, LibraryCirculationService $circulationService, ?Organization $organizationModel = null): RedirectResponse
    {
        $validated = $request->validate([
            'loan_id' => ['required', 'integer'],
            'issue_type' => ['required', 'in:lost,damaged'],
            'notes' => ['nullable', 'string'],
        ]);

        $loan = Loan::query()
            ->with(['member', 'bookCopy'])
            ->where('tenant_id', $tenantModel->id)
            ->whereKey((int) $validated['loan_id'])
            ->when($organizationModel !== null, function (Builder $query) use ($organizationModel): void {
                $query->where(function (Builder $builder) use ($organizationModel): void {
                    $builder->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationModel->id);
                });
            })
            ->firstOrFail();

        abort_if(! $loan->isActive(), 422, 'Loan ini tidak lagi aktif untuk ditandai bermasalah.');
        abort_if(! $loan->bookCopy, 422, 'Copy buku untuk loan ini tidak ditemukan.');

        $issueType = $validated['issue_type'];
        $issueLabel = $issueType === 'lost' ? 'hilang' : 'rusak';

        DB::transaction(function () use ($loan, $validated, $issueType): void {
            $existingNotes = trim((string) $loan->notes);
            $issueNotes = trim((string) ($validated['notes'] ?? ''));

            $loan->forceFill([
                'return_date' => now()->toDateString(),
                'returned_by' => auth()->id(),
                'status' => $issueType,
                'condition_on_return' => $issueType,
                'notes' => trim(implode("\n", array_filter([
                    $existingNotes,
                    $issueNotes !== '' ? '['.strtoupper($issueType).'] '.$issueNotes : null,
                ]))),
            ])->save();

            $loan->bookCopy->forceFill([
                'status' => $issueType,
                'condition' => $issueType,
                'notes' => trim(implode("\n", array_filter([
                    trim((string) $loan->bookCopy->notes),
                    $issueNotes !== '' ? '['.strtoupper($issueType).'] '.$issueNotes : null,
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

    protected function authorizeCirculation(?User $user, Tenant $tenant): void
    {
        abort_unless($user !== null && ($user->isGlobalSuperAdmin() || $user->canAccessTenant($tenant)), 403);
        abort_unless($user->isGlobalSuperAdmin() || $user->can('Create:Loan') || $user->can('Update:Loan') || $user->can('ViewAny:Loan'), 403);
    }

    protected function resolveBrandLogoUrl(Tenant $tenant, ?Organization $organization = null): ?string
    {
        $candidates = array_filter([
            $organization?->logo,
            $tenant->logo,
        ]);

        foreach ($candidates as $path) {
            $url = $this->pathToUrl((string) $path);
            if ($url !== null) {
                return $url;
            }
        }

        return null;
    }

    protected function pathToUrl(string $path): ?string
    {
        $value = trim($path);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, '/')) {
            return $value;
        }

        if (Storage::disk('public')->exists($value)) {
            return Storage::disk('public')->url($value);
        }

        return asset($value);
    }
}
