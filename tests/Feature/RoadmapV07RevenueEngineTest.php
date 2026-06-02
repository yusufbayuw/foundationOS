<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\Site;
use Modules\Cms\Services\CmsPublishService;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;
use Modules\Donation\Models\RecurringDonation;
use Modules\Donation\Services\DonationJournalService;
use Modules\Donation\Services\DonationPaymentService;
use Modules\Enrollment\Models\Lead;
use Modules\Facility\Models\FacilityRental;
use Modules\Facility\Models\Room;
use Modules\Facility\Services\FacilityRentalJournalService;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\Marketplace\Models\Seller;
use Modules\Marketplace\Services\MarketplaceOrderService;
use Modules\Sales\Models\CooperativeSaving;
use Modules\Sales\Models\Customer;
use Modules\Sales\Services\CooperativeJournalService;
use Modules\Training\Models\TrainingBatch;
use Modules\Training\Models\TrainingEnrollment;
use Modules\Training\Models\TrainingProgram;
use Modules\Training\Services\TrainingCertificateService;
use RuntimeException;
use Tests\TestCase;

class RoadmapV07RevenueEngineTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_inquiry_creates_lead_with_utm(): void
    {
        $tenant = $this->makeTenant();

        $response = $this->postJson('/inquiry', [
            'tenant_code' => $tenant->code,
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'utm_source' => 'google',
            'utm_campaign' => 'ppdb2026',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('leads', [
            'tenant_id' => $tenant->getKey(),
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        $lead = Lead::withoutTenantScope()->first();
        $this->assertSame('google', $lead->utm['utm_source'] ?? null);
    }

    public function test_donation_paid_posts_balanced_journal(): void
    {
        $tenant = $this->makeTenant();
        $org = $this->makeOrganization($tenant);
        $this->seedCoa($tenant, $org);

        $campaign = Campaign::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'CAMP-1',
            'name' => 'Beasiswa',
            'goal_amount' => 10000000,
        ]);

        $donor = Donor::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Donatur A',
        ]);

        $donation = Donation::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'donor_id' => $donor->getKey(),
            'donation_number' => 'DON-001',
            'amount' => 500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $entry = app(DonationJournalService::class)->postForPaidDonation($donation);

        $this->assertTrue($entry->is_balanced);
        $this->assertEqualsWithDelta(
            (float) $entry->total_debit,
            (float) $entry->total_credit,
            0.01,
        );
    }

    public function test_donation_webhook_is_idempotent(): void
    {
        $tenant = $this->makeTenant();
        $org = $this->makeOrganization($tenant);
        $this->seedCoa($tenant, $org);

        $campaign = Campaign::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'CAMP-2',
            'name' => 'Fasilitas',
        ]);

        $donor = Donor::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Donatur B',
        ]);

        Donation::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'donor_id' => $donor->getKey(),
            'donation_number' => 'DON-WH-1',
            'amount' => 100000,
            'payment_status' => 'pending',
        ]);

        $service = app(DonationPaymentService::class);
        $payload = ['order_id' => 'DON-WH-1', 'transaction_status' => 'settlement', 'transaction_id' => 'TX-1'];

        $first = $service->handleWebhook($payload);
        $second = $service->handleWebhook($payload);

        $this->assertSame('paid', $first->payment_status);
        $this->assertSame('paid', $second->payment_status);
        $this->assertSame(
            1,
            JournalEntry::withoutTenantScope()->where('entry_number', 'DON-DON-WH-1')->count(),
        );
    }

    public function test_cms_publish_requires_approval_gating(): void
    {
        $tenant = $this->makeTenant();
        $site = Site::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'main',
            'name' => 'Main Site',
            'is_active' => true,
        ]);

        $page = Page::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'slug' => 'ppdb',
            'title_id' => 'PPDB',
            'status' => 'pending_review',
        ]);

        $this->expectException(RuntimeException::class);
        app(CmsPublishService::class)->publishPage($page);
    }

    public function test_cms_published_page_is_public(): void
    {
        $tenant = $this->makeTenant();
        $site = Site::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'yayasan',
            'name' => 'Yayasan',
            'is_active' => true,
        ]);

        Page::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'slug' => 'home',
            'title_id' => 'Beranda',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/cms/yayasan/pages/home')->assertOk();
    }

    public function test_training_certificate_verification_endpoint(): void
    {
        $tenant = $this->makeTenant();

        $program = TrainingProgram::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'TP-1',
            'name' => 'Leadership',
        ]);

        $batch = TrainingBatch::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'training_program_id' => $program->getKey(),
            'code' => 'B1',
        ]);

        $enrollment = TrainingEnrollment::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'training_batch_id' => $batch->getKey(),
            'participant_name' => 'Ani',
            'status' => 'completed',
        ]);

        $cert = app(TrainingCertificateService::class)->issue($enrollment);

        $this->getJson('/training/certificates/'.$cert->verification_token)
            ->assertOk()
            ->assertJsonPath('valid', true);
    }

    public function test_marketplace_seller_order_isolation(): void
    {
        $tenant = $this->makeTenant();

        $sellerA = Seller::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'S-A',
            'name' => 'Seller A',
            'verification_status' => 'verified',
        ]);

        $sellerB = Seller::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'S-B',
            'name' => 'Seller B',
            'verification_status' => 'verified',
        ]);

        $orderB = MarketplaceOrder::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'seller_id' => $sellerB->getKey(),
            'code' => 'ORD-B',
            'name' => 'Order B',
        ]);

        $service = app(MarketplaceOrderService::class);

        $this->expectException(RuntimeException::class);
        $service->assertSellerIsolation($sellerA, $orderB);
    }

    public function test_recurring_donation_charge_is_idempotent(): void
    {
        $tenant = $this->makeTenant();
        $org = $this->makeOrganization($tenant);
        $this->seedCoa($tenant, $org);

        $campaign = Campaign::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'CAMP-R',
            'name' => 'Recurring',
        ]);

        $donor = Donor::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Donatur R',
        ]);

        RecurringDonation::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'donor_id' => $donor->getKey(),
            'amount' => 50000,
            'next_charge_date' => today(),
            'status' => 'active',
        ]);

        $this->artisan('donation:charge-recurring')->assertSuccessful();
        $this->artisan('donation:charge-recurring')->assertSuccessful();

        $this->assertSame(
            1,
            Donation::withoutTenantScope()->where('payment_reference', 'like', 'recurring-%')->count(),
        );
    }

    public function test_cooperative_savings_journal_balanced(): void
    {
        $tenant = $this->makeTenant();
        $org = $this->makeOrganization($tenant);
        $this->seedCoa($tenant, $org, includeEquity: true);

        $customer = Customer::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Anggota Koperasi',
            'is_cooperative_member' => true,
            'member_number' => 'M-001',
        ]);

        $saving = CooperativeSaving::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'customer_id' => $customer->getKey(),
            'savings_type' => 'pokok',
            'amount' => 100000,
            'transaction_date' => today(),
            'status' => 'posted',
        ]);

        $entry = app(CooperativeJournalService::class)->postSavings($saving);

        $this->assertEqualsWithDelta(
            (float) $entry->total_debit,
            (float) $entry->total_credit,
            0.01,
        );
    }

    public function test_facility_rental_journal_balanced(): void
    {
        $tenant = $this->makeTenant();
        $org = $this->makeOrganization($tenant);
        $this->seedCoa($tenant, $org);

        $room = Room::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'AUD',
            'name' => 'Auditorium',
            'is_rentable' => true,
            'rental_rate_daily' => 5000000,
        ]);

        $rental = FacilityRental::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'room_id' => $room->getKey(),
            'starts_at' => now(),
            'ends_at' => now()->addDay(),
            'total_amount' => 5000000,
            'status' => 'confirmed',
        ]);

        $entry = app(FacilityRentalJournalService::class)->postForRental($rental);

        $this->assertEqualsWithDelta(
            (float) $entry->total_debit,
            (float) $entry->total_credit,
            0.01,
        );
    }

    public function test_revenue_engine_scheduled_commands_register(): void
    {
        $this->artisan('enrollment:lead-followup-due')->assertSuccessful();
        $this->artisan('donation:send-campaign-update')->assertSuccessful();
        $this->artisan('training:settle-affiliate')->assertSuccessful();
        $this->artisan('printing:calculate-royalty-monthly')->assertSuccessful();
        $this->artisan('property:generate-monthly-lease-invoice')->assertSuccessful();
    }

    protected function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'starter-'.Str::random(4),
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        return Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-'.Str::random(6),
            'name' => 'Test Tenant',
            'subscription_plan_id' => $plan->getKey(),
        ]);
    }

    protected function makeOrganization(Tenant $tenant): Organization
    {
        return Organization::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'ORG-1',
            'name' => 'Main Org',
            'type' => 'school',
            'is_active' => true,
        ]);
    }

    protected function seedCoa(Tenant $tenant, Organization $org, bool $includeEquity = false): void
    {
        ChartOfAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $org->getKey(),
            'code' => '1101',
            'name' => 'Kas Bank',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        ChartOfAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $org->getKey(),
            'code' => '4101',
            'name' => 'Pendapatan Donasi',
            'type' => 'revenue',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        if ($includeEquity) {
            ChartOfAccount::query()->create([
                'tenant_id' => $tenant->getKey(),
                'organization_id' => $org->getKey(),
                'code' => '3101',
                'name' => 'Simpanan Koperasi',
                'type' => 'equity',
                'normal_balance' => 'credit',
                'is_active' => true,
            ]);
        }
    }
}
