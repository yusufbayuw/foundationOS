<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Asset\Models\Asset;
use Modules\Asset\Services\AssetDepreciationService;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Core\Support\QrCodeGenerator;
use Modules\EOffice\Models\Letter;
use Modules\EOffice\Services\LetterNumberingService;
use Modules\Facility\Models\Room;
use Modules\Facility\Models\RoomBooking;
use Modules\Facility\Services\RoomBookingConflictChecker;
use Modules\Helpdesk\Models\Ticket;
use Modules\Helpdesk\Models\TicketCategory;
use Modules\Legal\Services\Esign\EsignManager;
use Tests\TestCase;

class RoadmapV05FacilitiesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_asset_depreciation_service_creates_journal(): void
    {
        $tenant = $this->makeTenant();

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'ORG-1',
            'name' => 'Main Org',
            'type' => 'school',
            'is_active' => true,
        ]);

        $asset = Asset::query()->create([
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $organization->getKey(),
            'code' => 'AST-001',
            'name' => 'Projector',
            'acquisition_value' => 12000000,
            'useful_life_months' => 36,
            'depreciation_method' => 'straight-line',
            'status' => 'active',
        ]);

        $depreciation = app(AssetDepreciationService::class)->recordMonthlyDepreciation($asset);

        $this->assertDatabaseHas('asset_depreciations', [
            'id' => $depreciation->getKey(),
            'asset_id' => $asset->getKey(),
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $tenant->getKey(),
            'entry_number' => 'JE-DEP-'.$depreciation->getKey(),
        ]);
    }

    public function test_room_booking_conflict_detection(): void
    {
        $tenant = $this->makeTenant();

        $room = Room::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'R-101',
            'name' => 'Lab 1',
            'is_bookable' => true,
            'status' => 'active',
        ]);

        RoomBooking::query()->create([
            'tenant_id' => $tenant->getKey(),
            'room_id' => $room->getKey(),
            'start_at' => now()->addHour(),
            'end_at' => now()->addHours(2),
            'booking_status' => 'approved',
            'name' => 'Meeting',
            'status' => 'active',
        ]);

        $checker = app(RoomBookingConflictChecker::class);

        $this->assertTrue($checker->hasConflict(
            $room->getKey(),
            now()->addMinutes(90),
            now()->addHours(3),
        ));
    }

    public function test_letter_numbering_service_assigns_number(): void
    {
        $tenant = $this->makeTenant();

        $letter = Letter::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Outgoing memo',
            'direction' => 'OUT',
            'status' => 'draft',
        ]);

        $number = app(LetterNumberingService::class)->assignNextNumber($letter);

        $this->assertNotEmpty($number);
        $this->assertSame($number, $letter->fresh()->letter_number);
    }

    public function test_ticket_created_assigns_default_agent(): void
    {
        $tenant = $this->makeTenant();
        $agent = User::factory()->create();

        $category = TicketCategory::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'GEN',
            'name' => 'General',
            'status' => 'active',
            'default_assignee_user_id' => $agent->getKey(),
            'resolution_hours' => 24,
        ]);

        $ticket = Ticket::query()->create([
            'tenant_id' => $tenant->getKey(),
            'ticket_category_id' => $category->getKey(),
            'code' => 'TKT-001',
            'name' => 'Broken AC',
            'status' => 'open',
        ]);

        $this->assertSame($agent->getKey(), $ticket->fresh()->assigned_to_user_id);
    }

    public function test_esign_manager_manual_provider(): void
    {
        $result = app(EsignManager::class)->driver('manual')->requestSignature('contracts/sample.pdf');

        $this->assertSame('manual', $result['provider']);
        $this->assertSame('awaiting_upload', $result['status']);
    }

    public function test_qr_code_generator_returns_svg(): void
    {
        $svg = app(QrCodeGenerator::class)->svg('foundationos:asset:1');

        $this->assertStringContainsString('<svg', $svg);
    }

    public function test_it_incident_webhook_endpoint_accepts_payload(): void
    {
        $response = $this->postJson('/itops/monitoring/webhook', [
            'alert' => 'CPU high',
            'severity' => 'warning',
        ]);

        $response->assertOk()->assertJson(['accepted' => true]);
    }

    public function test_scheduled_facilities_commands_are_registered(): void
    {
        $this->artisan('asset:check-insurance-expiry')->assertSuccessful();
        $this->artisan('dms:archive-expired')->assertSuccessful();
        $this->artisan('itops:check-expiry')->assertSuccessful();
        $this->artisan('cafeteria:settle-tenants')->assertSuccessful();
        $this->artisan('safety:daily-rounds')->assertSuccessful();
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
}
