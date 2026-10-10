<?php

namespace Tests\Regression;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseNotificationJsonQueryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_database_notifications_support_filament_json_path_queries(): void
    {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'test',
            'notifiable_type' => 'user',
            'notifiable_id' => 1,
            'data' => json_encode(['format' => 'filament', 'title' => 'Test notification'], JSON_THROW_ON_ERROR),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame(
            1,
            DB::table('notifications')
                ->where('data->format', 'filament')
                ->whereNull('read_at')
                ->count(),
        );
    }
}
