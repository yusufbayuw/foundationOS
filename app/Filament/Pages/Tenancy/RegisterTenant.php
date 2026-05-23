<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\TenantAdminProvisioner;
use Modules\Core\Services\TenantModuleProvisioner;
use Modules\Core\Support\CurrencyFormatter;
use Modules\Core\Support\FilamentUi;

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

        $provisioner = app(TenantAdminProvisioner::class);
        $provisioner->ensureTenantOwnerRole($user, $tenant);
        $provisioner->assignShieldSuperAdmin($user, $tenant);
        app(TenantModuleProvisioner::class)->enableForTenant($tenant, ['core', 'global']);

        return $tenant;
    }
}
