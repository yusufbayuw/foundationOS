<?php

namespace App\Filament\Pages\Tenancy;

use BezhanSalleh\FilamentShield\Support\Utils as ShieldUtils;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Support\CurrencyFormatter;
use Modules\Core\Support\FilamentUi;
use Spatie\Permission\Models\Role;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return FilamentUi::text('Create Organization');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(FilamentUi::field('name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set(
                        'code',
                        Str::slug($state ?? '', '_')
                    )),

                TextInput::make('code')
                    ->label(FilamentUi::field('code'))
                    ->required()
                    ->maxLength(50)
                    ->unique(Tenant::class, 'code')
                    ->alphaDash(),

                Select::make('timezone')
                    ->label(FilamentUi::field('timezone'))
                    ->options(
                        collect(\DateTimeZone::listIdentifiers())->mapWithKeys(fn ($tz) => [$tz => $tz])
                    )
                    ->searchable()
                    ->default(config('app.timezone', 'Asia/Jakarta')),

                Select::make('locale')
                    ->label(FilamentUi::field('locale'))
                    ->options(['id' => 'Indonesia', 'en' => 'English'])
                    ->default('id')
                    ->required(),

                Select::make('currency')
                    ->label(FilamentUi::field('currency'))
                    ->options(CurrencyFormatter::options())
                    ->default('IDR')
                    ->required()
                    ->searchable(),
            ]);
    }

    protected function handleRegistration(array $data): Tenant
    {
        $user = auth()->user();

        $tenant = Tenant::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'code' => $data['code'],
            'timezone' => $data['timezone'] ?? 'Asia/Jakarta',
            'locale' => $data['locale'] ?? 'id',
            'currency' => $data['currency'] ?? 'IDR',
            'status' => 'active',
            'created_by' => $user->getKey(),
        ]);

        // Give the registering user super-admin permissions via Shield
        $this->assignSuperAdminRole($user, $tenant);

        return $tenant;
    }

    private function assignSuperAdminRole(mixed $user, Tenant $tenant): void
    {
        // Set team context for Spatie Permission
        setPermissionsTeamId($tenant->getKey());

        // Ensure the super_admin role exists for this panel
        $superAdminRoleName = ShieldUtils::getSuperAdminName();
        $role = Role::firstOrCreate(
            ['name' => $superAdminRoleName, 'guard_name' => 'web'],
            ['team_id' => $tenant->getKey()],
        );

        // Attach directly to avoid guard mismatch issues across contexts
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => get_class($user),
                'tenant_id' => $tenant->getKey(),
            ],
        ]);

        // Also create a domain-level TenantRole entry and link the user
        $tenantRole = TenantRole::firstOrCreate(
            ['tenant_id' => $tenant->getKey(), 'slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full access',
                'is_super_admin' => true,
                'permissions' => [],
            ]
        );

        UserTenantRole::firstOrCreate(
            ['user_id' => $user->getKey(), 'tenant_id' => $tenant->getKey(), 'tenant_role_id' => $tenantRole->getKey()],
            ['is_primary' => true, 'assigned_by' => $user->getKey(), 'assigned_at' => now()],
        );
    }
}
