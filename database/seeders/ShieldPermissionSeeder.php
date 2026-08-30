<?php

namespace Database\Seeders;

use App\Models\Permission;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Filament::setCurrentPanel('admin');

        $permissionNames = collect(FilamentShield::getAllResourcePermissionsWithLabels())
            ->keys()
            ->merge($this->pageOrWidgetPermissionNames(FilamentShield::getPages()))
            ->merge($this->pageOrWidgetPermissionNames(FilamentShield::getWidgets()))
            ->filter(fn (mixed $permission): bool => is_string($permission) && $permission !== '')
            ->unique()
            ->values();

        $now = now();

        $permissionNames
            ->map(fn (string $name): array => [
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->chunk(500)
            ->each(fn ($permissions) => Permission::query()->insertOrIgnore($permissions->all()));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  array<class-string, array<string, mixed>>|null  $entities
     * @return list<string>
     */
    private function pageOrWidgetPermissionNames(?array $entities): array
    {
        return collect($entities ?? [])
            ->flatMap(fn (array $entity): array => array_keys($entity['permissions'] ?? []))
            ->values()
            ->all();
    }
}
