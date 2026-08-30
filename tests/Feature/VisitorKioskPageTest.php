<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Modules\Core\Services\TenantAdminProvisioner;
use Modules\PhysicalSecurity\Filament\Pages\VisitorKioskPage;
use Modules\PhysicalSecurity\Models\Visitor;
use Modules\PhysicalSecurity\Models\VisitorLog;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class VisitorKioskPageTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_authorized_kiosk_check_in_persists_the_visitor_log_for_the_active_tenant(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->makeTenantContext(
            ['physicalsecurity'],
            'visitor-kiosk-plan',
        );

        app(TenantAdminProvisioner::class)->assignShieldSuperAdmin($user, $tenant);
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
        $user->unsetRelation('roles')->unsetRelation('permissions');

        Filament::setCurrentPanel('admin');
        $this->actingAs($user);
        app(CurrentTenant::class)->set($tenant);
        Filament::setTenant($tenant);

        Livewire::actingAs($user)
            ->test(VisitorKioskPage::class)
            ->fillForm([
                'name' => 'Siti Visitor',
                'id_number' => 'ID-1001',
                'purpose' => 'Parent meeting',
            ])
            ->call('checkIn')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $visitor = Visitor::query()->sole();
        $visitorLog = VisitorLog::query()->sole();

        $this->assertSame($tenant->getKey(), $visitor->tenant_id);
        $this->assertStringStartsWith('VIS-', $visitor->code);
        $this->assertSame('ID-1001', $visitor->meta['id_number']);
        $this->assertSame($visitor->getKey(), $visitorLog->visitor_id);
        $this->assertTrue($visitorLog->checked_in_at->isToday());
    }
}
