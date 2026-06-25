<?php

namespace Modules\Library\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Enums\LoanIssueType;
use Modules\Library\Http\Requests\OpacCatalogRequest;
use Modules\Library\Http\Requests\OpacCheckoutRequest;
use Modules\Library\Http\Requests\OpacCirculationSearchRequest;
use Modules\Library\Http\Requests\OpacExtendLoanRequest;
use Modules\Library\Http\Requests\OpacMarkLoanIssueRequest;
use Modules\Library\Http\Requests\OpacQuickReturnRequest;
use Modules\Library\Http\Requests\PlaceBookReservationRequest;
use Modules\Library\Models\Book;
use Modules\Library\Services\Opac\CirculationAuthorizationService;
use Modules\Library\Services\Opac\OpacBookDetailService;
use Modules\Library\Services\Opac\OpacCatalogService;
use Modules\Library\Services\Opac\OpacCheckoutService;
use Modules\Library\Services\Opac\OpacCirculationPageService;
use Modules\Library\Services\Opac\OpacContextResolver;
use Modules\Library\Services\Opac\OpacExtendLoanService;
use Modules\Library\Services\Opac\OpacMarkLoanIssueService;
use Modules\Library\Services\Opac\OpacQuickReturnService;
use Modules\Library\Services\Opac\OpacReservationService;
use Modules\Library\Support\CirculationPolicyResolver;
use Modules\Library\Support\LibraryCirculationService;

class PublicOpacController extends Controller
{
    public function __construct(
        private OpacContextResolver $context,
        private CirculationAuthorizationService $circulationAuthorization,
        private OpacCatalogService $catalogService,
        private OpacBookDetailService $bookDetailService,
        private OpacCirculationPageService $circulationPageService,
        private OpacReservationService $reservationService,
        private OpacCheckoutService $checkoutService,
        private OpacQuickReturnService $quickReturnService,
        private OpacExtendLoanService $extendLoanService,
        private OpacMarkLoanIssueService $markLoanIssueService,
    ) {}

    public function index(OpacCatalogRequest $request, string $tenant): View
    {
        $tenantModel = $this->requireTenant($tenant);

        return $this->catalogService->render($tenantModel, $request->filter());
    }

    public function organizationIndex(OpacCatalogRequest $request, string $tenant, string $organization): View
    {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);

        return $this->catalogService->render($tenantModel, $request->filter(), $organizationModel);
    }

    public function show(string $tenant, Book $book): View
    {
        $tenantModel = $this->requireTenant($tenant);
        $this->assertBookBelongsToTenant($book, $tenantModel);

        return $this->bookDetailService->render($tenantModel, $book);
    }

    public function organizationShow(string $tenant, string $organization, Book $book): View
    {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);
        $this->assertBookBelongsToTenant($book, $tenantModel);
        abort_unless($this->context->bookVisibleForOrganization($book, $organizationModel), 404);

        return $this->bookDetailService->render($tenantModel, $book, $organizationModel);
    }

    public function reserve(
        PlaceBookReservationRequest $request,
        string $tenant,
        Book $book,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        $tenantModel = $this->requireTenant($tenant);
        $this->assertBookBelongsToTenant($book, $tenantModel);
        $validated = $request->validated();

        return $this->reservationService->place(
            $tenantModel,
            $book,
            $circulationService,
            (int) $validated['member_id'],
            $validated['notes'] ?? null,
        );
    }

    public function organizationReserve(
        PlaceBookReservationRequest $request,
        string $tenant,
        string $organization,
        Book $book,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);
        $this->assertBookBelongsToTenant($book, $tenantModel);
        abort_unless($this->context->bookVisibleForOrganization($book, $organizationModel), 404);
        $validated = $request->validated();

        return $this->reservationService->place(
            $tenantModel,
            $book,
            $circulationService,
            (int) $validated['member_id'],
            $validated['notes'] ?? null,
            $organizationModel,
        );
    }

    public function circulation(OpacCirculationSearchRequest $request, string $tenant): View
    {
        $tenantModel = $this->requireTenant($tenant);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);

        return $this->circulationPageService->render($tenantModel, $request->filter());
    }

    public function organizationCirculation(OpacCirculationSearchRequest $request, string $tenant, string $organization): View
    {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);

        return $this->circulationPageService->render($tenantModel, $request->filter(), $organizationModel);
    }

    public function checkout(
        OpacCheckoutRequest $request,
        string $tenant,
        CirculationPolicyResolver $policyResolver,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        $tenantModel = $this->requireTenant($tenant);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);
        $validated = $request->validated();

        return $this->checkoutService->checkout(
            $tenantModel,
            $policyResolver,
            $circulationService,
            (int) $validated['member_id'],
            (int) $validated['book_copy_id'],
            $validated['notes'] ?? null,
        );
    }

    public function organizationCheckout(
        OpacCheckoutRequest $request,
        string $tenant,
        string $organization,
        CirculationPolicyResolver $policyResolver,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);
        $validated = $request->validated();

        return $this->checkoutService->checkout(
            $tenantModel,
            $policyResolver,
            $circulationService,
            (int) $validated['member_id'],
            (int) $validated['book_copy_id'],
            $validated['notes'] ?? null,
            $organizationModel,
        );
    }

    public function quickReturn(
        OpacQuickReturnRequest $request,
        string $tenant,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        $tenantModel = $this->requireTenant($tenant);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);

        return $this->quickReturnService->returnLoan(
            $tenantModel,
            $circulationService,
            (int) $request->validated('loan_id'),
        );
    }

    public function organizationQuickReturn(
        OpacQuickReturnRequest $request,
        string $tenant,
        string $organization,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);

        return $this->quickReturnService->returnLoan(
            $tenantModel,
            $circulationService,
            (int) $request->validated('loan_id'),
            $organizationModel,
        );
    }

    public function extendLoan(
        OpacExtendLoanRequest $request,
        string $tenant,
        CirculationPolicyResolver $policyResolver,
    ): RedirectResponse {
        $tenantModel = $this->requireTenant($tenant);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);

        return $this->extendLoanService->extend(
            $tenantModel,
            $policyResolver,
            (int) $request->validated('loan_id'),
        );
    }

    public function organizationExtendLoan(
        OpacExtendLoanRequest $request,
        string $tenant,
        string $organization,
        CirculationPolicyResolver $policyResolver,
    ): RedirectResponse {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);

        return $this->extendLoanService->extend(
            $tenantModel,
            $policyResolver,
            (int) $request->validated('loan_id'),
            $organizationModel,
        );
    }

    public function markIssue(
        OpacMarkLoanIssueRequest $request,
        string $tenant,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        $tenantModel = $this->requireTenant($tenant);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);
        $validated = $request->validated();

        return $this->markLoanIssueService->mark(
            $tenantModel,
            $circulationService,
            (int) $validated['loan_id'],
            LoanIssueType::from($validated['issue_type']),
            $validated['notes'] ?? null,
        );
    }

    public function organizationMarkIssue(
        OpacMarkLoanIssueRequest $request,
        string $tenant,
        string $organization,
        LibraryCirculationService $circulationService,
    ): RedirectResponse {
        [$tenantModel, $organizationModel] = $this->requireTenantOrganization($tenant, $organization);
        $this->circulationAuthorization->authorize($request->user(), $tenantModel);
        $validated = $request->validated();

        return $this->markLoanIssueService->mark(
            $tenantModel,
            $circulationService,
            (int) $validated['loan_id'],
            LoanIssueType::from($validated['issue_type']),
            $validated['notes'] ?? null,
            $organizationModel,
        );
    }

    protected function requireTenant(string $tenant): Tenant
    {
        $tenantModel = $this->context->resolveTenant($tenant);
        abort_unless($tenantModel !== null, 404);

        return $tenantModel;
    }

    /**
     * @return array{0: Tenant, 1: Organization}
     */
    protected function requireTenantOrganization(string $tenant, string $organization): array
    {
        $tenantModel = $this->requireTenant($tenant);
        $organizationModel = $this->context->resolveOrganization($tenantModel, $organization);
        abort_unless($organizationModel !== null, 404);

        return [$tenantModel, $organizationModel];
    }

    protected function assertBookBelongsToTenant(Book $book, Tenant $tenant): void
    {
        abort_unless((int) $book->tenant_id === (int) $tenant->id, 404);
    }
}
