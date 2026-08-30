<?php

namespace Database\Seeders;

use App\Integrations\Moodle\MoodleSyncContext;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        MoodleSyncContext::withoutObservers(fn () => $this->call([
            ShieldPermissionSeeder::class,
            MvpDemoSeeder::class,
        ]));
    }
}
