<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Facility\Exceptions\DuplicateRoomCodeException;
use Modules\Facility\Models\Room;
use Modules\Facility\Services\RoomRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class RoomRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_room_with_booking_flags(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'facility']);

        $room = app(RoomRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' a101 ',
            name: 'Auditorium A101',
            isBookable: true,
            isRentable: true,
        );

        $this->assertSame('A101', $room->code);
        $this->assertTrue($room->is_bookable);
        $this->assertTrue($room->is_rentable);
        $this->assertDatabaseHas(Room::class, [
            'id' => $room->id,
            'tenant_id' => $tenant->id,
            'code' => 'A101',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'facility']);

        $service = app(RoomRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'LAB1', 'Science Lab');

        $this->expectException(DuplicateRoomCodeException::class);

        $service->register($tenant->id, $organization->id, 'lab1', 'Science Lab duplicate');
    }
}
