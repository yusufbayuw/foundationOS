<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\OrganizationSetting;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\LibraryPolicy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Library\Support\CirculationPolicyResolver;
use Modules\Library\Support\LibraryCirculationService;
use Modules\Library\Support\LibraryScopeResolver;
use Modules\Library\Support\SlimsImportService;
use Tests\TestCase;

class LibraryFoundationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_library_scope_allows_tenant_wide_and_assigned_organization_records(): void
    {
        [$tenant, $organizationA, $organizationB, $user] = $this->makeTenantContext();

        $tenantWideBook = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Tenant Wide Book',
            'authors' => ['A'],
        ]);

        $organizationBook = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'title' => 'Org A Book',
            'authors' => ['A'],
        ]);

        Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationB->id,
            'title' => 'Org B Book',
            'authors' => ['B'],
        ]);

        $ids = app(LibraryScopeResolver::class)
            ->apply(Book::query(), $user, (int) $tenant->id)
            ->pluck('id')
            ->all();

        $this->assertEqualsCanonicalizing([$tenantWideBook->id, $organizationBook->id], $ids);
    }

    public function test_circulation_policy_prioritizes_organization_policy_over_tenant_policy(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'user_id' => $user->id,
            'member_number' => 'M-001',
            'max_books' => 0,
            'loan_period_days' => 0,
            'fine_per_day' => 0,
        ]);

        LibraryPolicy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'name' => 'Tenant Default',
            'max_books' => 3,
            'loan_period_days' => 7,
            'fine_per_day' => 1000,
            'max_extensions' => 2,
            'grace_period_days' => 1,
            'reservation_pickup_days' => 2,
        ]);

        LibraryPolicy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'name' => 'Org Policy',
            'max_books' => 5,
            'loan_period_days' => 10,
            'fine_per_day' => 2500,
            'max_extensions' => 3,
            'grace_period_days' => 2,
            'reservation_pickup_days' => 4,
        ]);

        $policy = app(CirculationPolicyResolver::class)->resolveForMember($member);

        $this->assertSame(5, $policy['max_books']);
        $this->assertSame(10, $policy['loan_period_days']);
        $this->assertSame(2500.0, $policy['fine_per_day']);
        $this->assertSame(3, $policy['max_extensions']);
        $this->assertSame(2, $policy['grace_period_days']);
    }

    public function test_reservation_is_marked_ready_when_stock_becomes_available(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Distributed Systems',
            'authors' => ['Tanenbaum'],
            'total_copies' => 1,
            'available_copies' => 0,
        ]);

        $copy = BookCopy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'book_id' => $book->id,
            'copy_number' => 'C-001',
            'status' => 'available',
        ]);

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'user_id' => $user->id,
            'member_number' => 'M-READY',
        ]);

        $reservation = app(LibraryCirculationService::class)->placeReservation($member, $book);
        $this->assertSame('pending', $reservation->status);

        app(LibraryCirculationService::class)->refreshBookAvailability((int) $book->id);

        $reservation->refresh();
        $copy->refresh();
        $book->refresh();

        $this->assertSame('ready', $reservation->status);
        $this->assertNotNull($reservation->ready_at);
        $this->assertSame(1, $book->available_copies);
    }

    public function test_recalculate_fines_command_updates_overdue_loan(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Domain-Driven Design',
            'authors' => ['Evans'],
        ]);

        $copy = BookCopy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'book_id' => $book->id,
            'copy_number' => 'C-OVERDUE',
            'status' => 'available',
        ]);

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'user_id' => $user->id,
            'member_number' => 'M-OD',
            'fine_per_day' => 1000,
            'loan_period_days' => 7,
        ]);

        $loan = Loan::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'book_copy_id' => $copy->id,
            'member_id' => $member->id,
            'loan_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(3)->toDateString(),
            'status' => 'borrowed',
        ]);

        $this->artisan('fos:library:recalc-fines --tenant='.$tenant->id)
            ->assertExitCode(0);

        $loan->refresh();
        $member->refresh();

        $this->assertSame('overdue', $loan->status);
        $this->assertGreaterThan(0, (float) $loan->fine_amount);
        $this->assertGreaterThan(0, (float) $member->unpaid_fines);
    }

    public function test_opac_page_is_tenant_aware(): void
    {
        [$tenant] = $this->makeTenantContext();

        Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Pragmatic Programmer',
            'authors' => ['Hunt'],
            'is_active' => true,
        ]);

        $response = $this->get('/opac/'.$tenant->code.'?q=Pragmatic');

        $response->assertOk();
        $response->assertSee('Pragmatic Programmer');
        $response->assertSee($tenant->name);
    }

    public function test_organization_opac_page_shows_organization_and_tenant_wide_books_only(): void
    {
        [$tenant, $organizationA, $organizationB] = $this->makeTenantContext();

        Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Tenant Wide Catalog',
            'authors' => ['Shared'],
            'is_active' => true,
        ]);

        Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'title' => 'Organization A Catalog',
            'authors' => ['Org A'],
            'is_active' => true,
        ]);

        Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationB->id,
            'title' => 'Organization B Catalog',
            'authors' => ['Org B'],
            'is_active' => true,
        ]);

        $response = $this->get('/opac/'.$tenant->code.'/organizations/'.$organizationA->code);

        $response->assertOk();
        $response->assertSee('Tenant Wide Catalog');
        $response->assertSee('Organization A Catalog');
        $response->assertDontSee('Organization B Catalog');
        $response->assertSee($organizationA->name);
    }

    public function test_opac_can_filter_available_books_only(): void
    {
        [$tenant] = $this->makeTenantContext();

        Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Available Catalog',
            'authors' => ['Shared'],
            'available_copies' => 2,
            'total_copies' => 2,
            'is_active' => true,
        ]);

        Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Unavailable Catalog',
            'authors' => ['Shared'],
            'available_copies' => 0,
            'total_copies' => 2,
            'is_active' => true,
        ]);

        $response = $this->get('/opac/'.$tenant->code.'?available_only=1');

        $response->assertOk();
        $response->assertSee('Available Catalog');
        $response->assertDontSee('Unavailable Catalog');
    }

    public function test_circulation_page_is_accessible_for_global_super_admin(): void
    {
        [$tenant] = $this->makeTenantContext();

        $user = User::factory()->create();

        $user->promoteToGlobalSuperAdmin();

        $response = $this->actingAs($user)->get('/opac/'.$tenant->code.'/circulation');

        $response->assertOk();
        $response->assertSee('Checkout dan return cepat');
    }

    public function test_circulation_extend_loan_updates_due_date_and_extension_count(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $user->forceFill(['is_super_admin' => true])->save();

        LibraryPolicy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'name' => 'Org Policy',
            'max_books' => 5,
            'loan_period_days' => 10,
            'fine_per_day' => 2500,
            'max_extensions' => 3,
            'grace_period_days' => 2,
            'reservation_pickup_days' => 4,
            'is_active' => true,
        ]);

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'user_id' => $user->id,
            'member_number' => 'M-EXT',
            'status' => 'active',
        ]);

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Refactoring',
            'authors' => ['Fowler'],
            'is_active' => true,
        ]);

        $copy = BookCopy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'book_id' => $book->id,
            'copy_number' => 'COPY-EXT',
            'status' => 'borrowed',
        ]);

        $loan = Loan::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'book_copy_id' => $copy->id,
            'member_id' => $member->id,
            'loan_date' => now()->subDays(2)->toDateString(),
            'due_date' => now()->addDay()->toDateString(),
            'status' => 'borrowed',
            'extension_count' => 0,
            'max_extensions' => 3,
        ]);

        $response = $this->actingAs($user)->post('/opac/'.$tenant->code.'/circulation/extend', [
            'loan_id' => $loan->id,
        ]);

        $response->assertRedirect();

        $loan->refresh();

        $this->assertSame(1, $loan->extension_count);
        $this->assertSame(now()->addDays(11)->toDateString(), $loan->due_date?->toDateString());
    }

    public function test_circulation_issue_marks_copy_and_loan_as_lost(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $user->forceFill(['is_super_admin' => true])->save();

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'user_id' => $user->id,
            'member_number' => 'M-ISSUE',
            'status' => 'active',
        ]);

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Clean Architecture',
            'authors' => ['Martin'],
            'is_active' => true,
        ]);

        $copy = BookCopy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'book_id' => $book->id,
            'copy_number' => 'COPY-ISSUE',
            'status' => 'borrowed',
        ]);

        $loan = Loan::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'book_copy_id' => $copy->id,
            'member_id' => $member->id,
            'loan_date' => now()->subDays(3)->toDateString(),
            'due_date' => now()->addDays(4)->toDateString(),
            'status' => 'borrowed',
            'extension_count' => 0,
            'max_extensions' => 2,
        ]);

        $response = $this->actingAs($user)->post('/opac/'.$tenant->code.'/circulation/issue', [
            'loan_id' => $loan->id,
            'issue_type' => 'lost',
            'notes' => 'Buku tidak kembali setelah audit internal.',
        ]);

        $response->assertRedirect();

        $loan->refresh();
        $copy->refresh();

        $this->assertSame('lost', $loan->status);
        $this->assertNotNull($loan->return_date);
        $this->assertSame('lost', $loan->condition_on_return);
        $this->assertSame('lost', $copy->status);
        $this->assertSame('lost', $copy->condition);
    }

    public function test_slims_config_prioritizes_organization_settings_when_organization_is_provided(): void
    {
        [$tenant, $organizationA] = $this->makeTenantContext();

        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'integration',
            'key' => 'slims_import_enabled',
            'value' => 'true',
            'type' => 'boolean',
        ]);
        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'integration',
            'key' => 'slims_db_database',
            'value' => 'tenant_slims',
            'type' => 'string',
        ]);
        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'integration',
            'key' => 'slims_db_username',
            'value' => 'tenant_user',
            'type' => 'string',
        ]);

        OrganizationSetting::create([
            'organization_id' => $organizationA->id,
            'group' => 'integration',
            'key' => 'slims_import_enabled',
            'value' => 'true',
            'type' => 'boolean',
        ]);
        OrganizationSetting::create([
            'organization_id' => $organizationA->id,
            'group' => 'integration',
            'key' => 'slims_db_database',
            'value' => 'organization_slims',
            'type' => 'string',
        ]);
        OrganizationSetting::create([
            'organization_id' => $organizationA->id,
            'group' => 'integration',
            'key' => 'slims_db_username',
            'value' => 'organization_user',
            'type' => 'string',
        ]);

        $config = app(SlimsImportService::class)->resolveConfig((int) $tenant->id, (int) $organizationA->id);

        $this->assertNotNull($config);
        $this->assertSame('organization_slims', $config['connection']['database']);
        $this->assertSame('organization_user', $config['connection']['username']);
    }

    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'library-plan',
            'name' => 'Library Plan',
            'included_modules' => ['core', 'library'],
        ]);

        $user = User::create([
            'name' => 'Library Admin',
            'email' => 'library-admin@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-library',
            'name' => 'Tenant Library',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organizationA = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-a',
            'name' => 'Organization A',
        ]);

        $organizationB = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-b',
            'name' => 'Organization B',
        ]);

        $tenantRole = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Library Admin',
            'slug' => 'library-admin',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'tenant_role_id' => $tenantRole->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $organizationA, $organizationB, $user];
    }
}
